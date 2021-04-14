<?php
namespace App\Services;

use App\Models\FinanceMutation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceMutationService {

    public function get($params = []) {
        $query_builder = FinanceMutation::with(['labels','project'])->whereNull('deleted_at');
        foreach($params as $field => $val) {
            if(isset($val)) {
                switch($field) {
                    case 'name':
                        $query_builder->where($field,'like',"%{$val}%");
                    break;
                    case 'date_from':
                        $query_builder->where('mutation_date','>=',$val);
                    break;
                    case 'date_to':
                        $query_builder->where('mutation_date','<=',$val);
                    break;
                    case 'label_id':
                        $query_builder->whereHas('labels', function(Builder $query) use($val){
                            $query->where('finance_labels.id',$val)
                                ->whereNull('finance_labels.deleted_at');
                        });
                    break;
                    case 'project_id':
                        if($val == "0") $query_builder->where('project_id', null);
                        else $query_builder->where('project_id', $val);
                    break;
                    default:
                        $query_builder->where($field,$val);
                    break;
                }
            }
        }
        $finance_mutations = $query_builder->orderBy('mutation_date','desc')
            ->get();

        return $finance_mutations;
    }

    public function detail($id) {
        $finance_mutation = FinanceMutation::with(['labels'])
            ->whereNull('deleted_at')->where('id',$id)->first();
        return $finance_mutation;
    }

    public function save($attr) {
        try{
            $finance_mutation = null;
            DB::transaction(function () use(&$finance_mutation, $attr){
                if(isset($attr['id'])) {
                    $finance_mutation = FinanceMutation::find($attr['id']);
                    if(isset($attr['finance_label_ids'])) {
                        if(empty($attr['finance_label_ids']))
                            $finance_mutation->labels()->detach();
                        else
                            $finance_mutation->labels()->sync($attr['finance_label_ids']);
                    }
                    if(array_key_exists('finance_label_ids',$attr)) unset($attr['finance_label_ids']);
                    $attr['updated_by'] = Auth::user()->id;
                    $finance_mutation->fill($attr);
                    $finance_mutation->save();
                } else {
                    $finance_label_ids = null;
                    if(!empty($attr['finance_label_ids'])) {
                        $finance_label_ids = $attr['finance_label_ids'];
                        unset($attr['finance_label_ids']);
                    }
                    if(array_key_exists('finance_label_ids',$attr)) unset($attr['finance_label_ids']);
                    $attr['created_by'] = Auth::user()->id;
                    $attr['updated_by'] = $attr['created_by'];
                    $finance_mutation = FinanceMutation::create($attr);
                    if(!empty($finance_label_ids))
                        $finance_mutation->labels()->sync($finance_label_ids);
                }
            });
            return $finance_mutation;
        } catch(\Exception $e) {
            throw new \Exception("Failed to save data", 500);
        }
    }

    public function delete($id) {
        try{
            DB::transaction(function() use($id){
                $user_id = Auth::user()->id;
                $now = Carbon::now();
                $finance_mutation = FinanceMutation::find($id);
                $finance_mutation->deleted_by = $user_id;
                $finance_mutation->deleted_at = $now;
                $finance_mutation->labels()->detach();
                $finance_mutation->save();
            });
            return true;
        }catch(\Exception $e) {
            throw new \Exception("Failed to delete data", 500);
        }
    }

}
