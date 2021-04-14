<?php
namespace App\Services;

use App\Models\FinanceMutation;
use App\Models\FinanceMutationSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceMutationScheduleService {

    public function get($params = []) {
        $query_builder = FinanceMutationSchedule::whereNull('deleted_at');
        foreach($params as $field => $val) {
            if(isset($val)) {
                switch($field) {
                    case 'name':
                        $query_builder->where($field,'like',"%{$val}%");
                    break;
                    default:
                        $query_builder->where($field,$val);
                    break;
                }
            }
        }
        $finance_mutation_schedules = $query_builder->orderBy('next_mutation_date','desc')
            ->get();

        return $finance_mutation_schedules;
    }

    public function detail($id) {
        $finance_mutation_schedules = FinanceMutationSchedule::whereNull('deleted_at')
            ->where('id',$id)->first();
        return $finance_mutation_schedules;
    }

    public function runTodaySchedule() {
        $now = Carbon::now()->addDay(1);
        $finance_mutation_schedules = FinanceMutationSchedule::whereNull('deleted_at')
            ->where('next_mutation_date',$now->toDateString());
        if(!$finance_mutation_schedules->exists()) return;
        try{
            DB::transaction(function () use($finance_mutation_schedules){
                foreach($finance_mutation_schedules->cursor() as $finance_mutation_schedule) {
                    $finance_mutation = FinanceMutation::create([
                        'mutation_date' => $finance_mutation_schedule->next_mutation_date,
                        'name' => $finance_mutation_schedule->name,
                        'nominal' => $finance_mutation_schedule->nominal,
                        'mode' => $finance_mutation_schedule->mode,
                        'project_id' => $finance_mutation_schedule->project_id,
                        'notes' => $finance_mutation_schedule->notes,
                        'created_by' => 0,
                        'updated_by' => 0
                    ]);
                    if(!empty($finance_mutation_schedule->attached_label_ids)) {
                        $finance_mutation->labels()->attach($finance_mutation_schedule->attached_label_ids);
                    }

                    switch($finance_mutation_schedule->repeat) {
                        case 'daily':
                            $finance_mutation_schedule->next_mutation_date = Carbon::parse($finance_mutation_schedule->next_mutation_date)->addDay(1);
                        break;
                        case 'weekly':
                            $finance_mutation_schedule->next_mutation_date = Carbon::parse($finance_mutation_schedule->next_mutation_date)->addWeek(1);
                        break;
                        case 'monthly':
                            $finance_mutation_schedule->next_mutation_date = Carbon::parse($finance_mutation_schedule->next_mutation_date)->addMonth(1);
                        break;
                        case 'yearly':
                            $finance_mutation_schedule->next_mutation_date = Carbon::parse($finance_mutation_schedule->next_mutation_date)->addYear(1);
                        break;
                    }
                    $finance_mutation_schedule->updated_by = 0;
                    $finance_mutation_schedule->save();
                }
            });
        } catch(\Exception $e) {
            throw new \Exception('Failed to save data');
        }
    }

    public function save($attr) {
        try{
            $finance_mutation_schedule = null;
            DB::transaction(function () use(&$finance_mutation_schedule, $attr){
                if(isset($attr['id'])) {
                    $finance_mutation_schedule = FinanceMutationSchedule::find($attr['id']);
                    $attr['updated_by'] = Auth::user()->id;
                    $finance_mutation_schedule->fill($attr);
                    $finance_mutation_schedule->save();
                } else {
                    $attr['created_by'] = Auth::user()->id;
                    $attr['updated_by'] = $attr['created_by'];
                    $finance_mutation_schedule = FinanceMutationSchedule::create($attr);
                }
            });
            return $finance_mutation_schedule;
        } catch(\Exception $e) {
            throw new \Exception("Failed to save data: ".$e->getMessage(), 500);
        }
    }

    public function delete($id) {
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        $finance_mutation_schedule = FinanceMutationSchedule::find($id);
        $finance_mutation_schedule->deleted_by = $user_id;
        $finance_mutation_schedule->deleted_at = $now;
        return $finance_mutation_schedule->save();
    }

}
