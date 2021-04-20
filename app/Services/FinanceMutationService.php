<?php
namespace App\Services;

use App\Models\FinanceMutation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FinanceMutationService {

    public function statistic($currency) {
        if(!in_array($currency,['usd','cny','idr'])) $currency = 'usd';
        $total_debit = FinanceMutation::totalDebit($currency);
        $total_credit = FinanceMutation::totalCredit($currency);
        $total_earn = $total_debit + $total_credit;
        return compact('currency','total_earn','total_debit','total_credit');
    }

    public function periodicStatistic($date_from, $date_to, $periode, $currency) {
        if(!in_array($currency,['usd','cny','idr'])) $currency = 'usd';
        $query_builder = FinanceMutation::whereBetween('mutation_date', [$date_from, $date_to])
            ->select("currency","usd_cny","usd_idr","cny_usd","cny_idr","idr_usd","idr_cny")
            ->selectRaw("SUM(CASE WHEN `mode` = 'credit' THEN nominal*-1 ELSE 0 END) as total_credit")
            ->selectRaw("SUM(CASE WHEN `mode` = 'debit' THEN nominal ELSE 0 END) as total_debit")
            ->selectRaw("SUM(CASE WHEN `mode` = 'debit' THEN nominal ELSE 0 END) + SUM(CASE WHEN `mode` = 'credit' THEN nominal*-1 ELSE 0 END) as total_earn")
            ->groupBy("currency","usd_cny","usd_idr","cny_usd","cny_idr","idr_usd","idr_cny")
            ->orderBy('mutation_date','asc');

        $datasets = [];
        $date_from = Carbon::parse($date_from);
        $date_to = Carbon::parse($date_to);
        switch($periode) {
            case 'weekly':
                $diff_days = $date_from->diffInDays($date_to)+1;
                $day = 1;
                $week = 1;
                for($i=0;$i<$diff_days;$i++) {
                    if($i != 0) $date_from->addDay();
                    if($day == 1) {
                        $datasets['week-'.$week] = [
                            'label' => 'Week '.$week,
                            'total_earn' => 0,
                            'total_debit' => 0,
                            'total_credit' => 0
                        ];
                    }
                    $mutations = clone $query_builder;
                    foreach($mutations->where('mutation_date',$date_from->format('Y-m-d'))->cursor() as $mutation) {
                        $conversion_rate = $mutation->{$mutation->currency."_".$currency} ?? 1;
                        $datasets['week-'.$week]['total_earn'] += $mutation->total_earn * $conversion_rate;
                        $datasets['week-'.$week]['total_debit'] += $mutation->total_debit * $conversion_rate;
                        $datasets['week-'.$week]['total_credit'] += $mutation->total_credit * $conversion_rate;
                    }
                    if($day == 7) {
                        $day = 1;
                        $week++;
                    } else {
                        $day ++;
                    }
                    unset($mutations);
                }
                break;
            case 'monthly':
                $diff_months = $date_from->diffInMonths($date_to)+1;
                for($i=0;$i<$diff_months;$i++) {
                    if($i != 0) $date_from->addMonth();
                    $datasets[$date_from->format('m-Y')] = [
                        'label' => $date_from->format('M Y'),
                        'total_earn' => 0,
                        'total_debit' => 0,
                        'total_credit' => 0
                    ];
                    $mutations = clone $query_builder;
                    foreach($mutations->whereRaw("DATE_FORMAT(mutation_date,'%m-%Y') like ?",[$date_from->format('m-Y')])->cursor() as $mutation) {
                        $conversion_rate = $mutation->currency."_".$currency;
                        $conversion_rate = ($mutation->$conversion_rate ?? 1);
                        $datasets[$date_from->format('m-Y')]['total_earn'] += $mutation->total_earn * $conversion_rate;
                        $datasets[$date_from->format('m-Y')]['total_debit'] += $mutation->total_debit * $conversion_rate;
                        $datasets[$date_from->format('m-Y')]['total_credit'] += $mutation->total_credit * $conversion_rate;
                    }
                    unset($mutations);
                }
                break;
        }
        return array_values($datasets);
    }

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
        $query_builder = $query_builder->orderBy('mutation_date','desc');
        $finance_mutations = [];
        foreach($query_builder->cursor() as $mutation) {
            $convert_to_usd = $mutation->{$mutation->currency."_usd"} ?? 1;
            $convert_to_cny = $mutation->{$mutation->currency."_cny"} ?? 1;
            $convert_to_idr = $mutation->{$mutation->currency."_idr"} ?? 1;
            $finance_mutations[] = [
                'id' => $mutation->id,
                'mutation_date' => Carbon::parse($mutation->mutation_date)->format('Y-m-d'),
                'name' => $mutation->name,
                'usd' => $mutation->nominal * $convert_to_usd,
                'cny' => $mutation->nominal * $convert_to_cny,
                'idr' => $mutation->nominal * $convert_to_idr,
                'mode' => $mutation->mode,
                'labels' => $mutation->labels,
                'project' => $mutation->project
            ];
        }
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
                    $this->currencyConversion($attr);
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
            throw new \Exception("Failed to save data: ".$e->getMessage(), 500);
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

    private function currencyConversion(&$attr) {
        $available_currencies = [
            'usd_cny,usd_idr',
            'cny_usd,cny_idr',
            'idr_usd,idr_cny'
        ];
        $url = config("services.currconv.domain")."/convert";
        $queries = [
            "compact" => "ultra",
            "apiKey" => config("services.currconv.key")
        ];

        $last_rate = FinanceMutation::whereNull('deleted_at')
            ->select('usd_cny','usd_idr','cny_usd','cny_idr','idr_usd','idr_cny','conversion_datetime')
            ->orderBy('conversion_datetime','desc')
            ->first();
        if(!$last_rate OR Carbon::now()->diffInMinutes($last_rate->conversion_datetime) > 60 ) {
            foreach($available_currencies as $currency) {
                $queries["q"] = $currency;
                $currencies = explode(",",$currency);
                $response = Http::get($url,$queries);
                if($response->successful()) {
                    $result = $response->json();
                    $attr[$currencies[0]] = $result[strtoupper($currencies[0])];
                    $attr[$currencies[1]] = $result[strtoupper($currencies[1])];
                }
            }
            $attr['conversion_datetime'] = Carbon::now();
        } else {
            $attr['usd_cny'] = $last_rate->usd_cny;
            $attr['usd_idr'] = $last_rate->usd_idr;
            $attr['cny_usd'] = $last_rate->cny_usd;
            $attr['cny_idr'] = $last_rate->cny_idr;
            $attr['idr_usd'] = $last_rate->idr_usd;
            $attr['idr_cny'] = $last_rate->idr_cny;
            $attr['conversion_datetime'] = $last_rate->conversion_datetime;
        }
    }
}