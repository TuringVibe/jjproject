<?php
namespace App\Services;

use App\Models\AssetPriceChange;
use App\Models\FinanceAsset;
use App\Models\FinanceLabel;
use App\Models\FinanceMutation;
use Carbon\Carbon;

class FinanceDashboardService {

    private $currencies = [
        'usd' => '&#36;',
        'cny' => '&yen;',
        'idr' => 'Rp'
    ];

    public function mutationStatistic($currency) {
        if(!in_array($currency,['usd','cny','idr'])) $currency = 'usd';
        $total_debit = FinanceMutation::totalDebit($currency);
        $total_credit = FinanceMutation::totalCredit($currency);
        $total_earning = $total_debit + $total_credit;
        return [
            'currency' => $this->currencies[$currency],
            'total_earning' => number_format($total_earning, 2),
            'total_debit' => number_format($total_debit, 2),
            'total_credit' => number_format($total_credit, 2)
        ];
    }

    public function assetStatistic($currency) {
        if(!in_array($currency,['usd','cny','idr'])) $currency = 'usd';
        $assets = [];
        foreach(FinanceAsset::whereNull('deleted_at')->cursor() as $asset) {
            $conversion_rate = $asset->{$asset->currency."_".$currency} ?? 1;
            $buy_price = $asset->buy_price_per_unit * $conversion_rate;
            $latest_price = $buy_price;
            $latest_price_change_datetime = $asset->buy_datetime;

            $latest_price_change = $asset->price_changes()->latest('change_datetime')->first();
            if(isset($latest_price_change)) {
                $latest_price_conversion_rate = $latest_price_change->{$latest_price_change->currency."_".$currency} ?? 1;
                $latest_price = $latest_price_change->price_per_unit * $latest_price_conversion_rate;
                $latest_price_change_datetime = $latest_price_change->change_datetime;
            }

            $assets[] = [
                'currency' => $this->currencies[$currency],
                'name' => $asset->name,
                'buy_price' => number_format($buy_price,2),
                'latest_price' =>  number_format($latest_price,2),
                'latest_price_change_datetime' => $latest_price_change_datetime->format('d M Y, H:i'),
                'percentage' => number_format(($latest_price - $buy_price)/$buy_price * 100,2)
            ];
        }
        return $assets;
    }

    public function assetPeriodicStatistic($date_from, $date_to, $periode, $currency) {
        if(!in_array($currency,['usd','cny','idr'])) $currency = 'usd';
        $assets = FinanceAsset::whereNull('deleted_at')->select('id','name')->get();
        if($assets->count() == 0) return [];

        $query_builder = AssetPriceChange::whereNull('deleted_at')
            ->orderBy('change_datetime','asc');

        $result = [
            'labels' => [],
            'datasets' => [],
            'data' => []
        ];
        foreach($assets as $asset) {
            $result['datasets'][$asset->id] = $asset->name;
        }
        $date_from = Carbon::parse($date_from);
        $date_to = Carbon::parse($date_to);
        switch($periode) {
            case 'weekly':
                $diff_days = $date_from->diffInDays($date_to)+1;
                $day = 1;
                $week = 1;
                for($i=0;$i<$diff_days;$i++) {
                    if($i != 0) $date_from->addDay();
                    $label = 'Week '.$week;
                    if($day == 1) {
                        $result['labels'][$week-1] = $label;
                        foreach($result['datasets'] as $asset_id => $asset_name) {
                            $result['data'][$asset_id][$week-1] = 0;
                        }
                    }
                    $price_changes = clone $query_builder;
                    $datetime = $date_from;
                    foreach($price_changes->whereRaw("DATE_FORMAT(change_datetime,'%Y-%m-%d') like ?",[$date_from->format('Y-m-d')])->cursor() as $price_change) {
                        $conversion_rate = $price_change->{$price_change->currency."_".$currency} ?? 1;
                        if(array_key_exists($price_change->finance_asset_id, $result['data'])) {
                            if($price_change->change_datetime >= $datetime) {
                                $result['data'][$price_change->finance_asset_id][$week-1] = $price_change->price_per_unit * $conversion_rate;
                                $datetime = $price_change->change_datetime;
                            }
                        }
                    }
                    if($day == 7) {
                        $day = 1;
                        $week++;
                    } else {
                        $day++;
                    }
                    unset($price_changes);
                }
                $result['data'] = array_values($result['data']);
                $result['datasets'] = array_values($result['datasets']);
                break;
            case 'monthly':
                $diff_months = $date_from->diffInMonths($date_to) + 1;
                for($i=0;$i<$diff_months;$i++) {
                    if($i != 0) $date_from->addMonth();
                    $label = $date_from->format('M Y');
                    $key = $date_from->format('m-Y');
                    $result['labels'][] = $label;
                    foreach($result['datasets'] as $asset_id => $asset_name) {
                        $result['data'][$asset_id][$i] = 0;
                    }
                    $price_changes = clone $query_builder;
                    $datetime = $date_from;
                    foreach($price_changes->whereRaw("DATE_FORMAT(change_datetime,'%m-%Y') like ?",[$key])->cursor() as $price_change) {
                        $conversion_rate = $price_change->{$price_change->currency."_".$currency} ?? 1;
                        if(array_key_exists($price_change->finance_asset_id, $result['data'])) {
                            if($price_change->change_datetime >= $datetime) {
                                $result['data'][$price_change->finance_asset_id][$i] = $price_change->price_per_unit * $conversion_rate;
                                $datetime = $price_change->change_datetime;
                            }
                        }
                    }
                    unset($price_changes);
                }
                $result['data'] = array_values($result['data']);
                $result['datasets'] = array_values($result['datasets']);
                break;
        }
        return $result;
    }

    public function mutationPeriodicStatistic($date_from, $date_to, $periode, $currency) {
        if(!in_array($currency,['usd','cny','idr'])) $currency = 'usd';
        $query_builder_all_mutations_before_date = FinanceMutation::whereNull('deleted_at')
            ->where('mutation_date','<', $date_from);
        $total_earnings = 0;
        foreach($query_builder_all_mutations_before_date->cursor() as $mutation) {
            $conversion_rate = $mutation->{$mutation->currency."_".$currency} ?? 1;
            if($mutation->mode == 'debit') {
                $total_earnings +=  ($mutation->nominal * $conversion_rate);
            } else {
                $total_earnings -=  ($mutation->nominal * $conversion_rate);
            }
        }

        $query_builder_mutations = FinanceMutation::whereNull('deleted_at')
            ->whereBetween('mutation_date', [$date_from, $date_to])
            ->select("currency","usd_cny","usd_idr","cny_usd","cny_idr","idr_usd","idr_cny")
            ->selectRaw("SUM(CASE WHEN `mode` = 'credit' THEN nominal ELSE 0 END) as total_credit")
            ->selectRaw("SUM(CASE WHEN `mode` = 'debit' THEN nominal ELSE 0 END) as total_debit")
            ->selectRaw("SUM(CASE WHEN `mode` = 'debit' THEN nominal ELSE 0 END) + SUM(CASE WHEN `mode` = 'credit' THEN nominal*-1 ELSE 0 END) as total_earning")
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
                            'total_earning' => 0,
                            'total_debit' => 0,
                            'total_credit' => 0,
                        ];
                    }
                    $mutations = clone $query_builder_mutations;
                    foreach($mutations->where('mutation_date',$date_from->format('Y-m-d'))->cursor() as $mutation) {
                        $conversion_rate = $mutation->{$mutation->currency."_".$currency} ?? 1;
                        $total_earnings += $mutation->total_earning * $conversion_rate;
                        $datasets['week-'.$week]['total_debit'] += $mutation->total_debit * $conversion_rate;
                        $datasets['week-'.$week]['total_credit'] += $mutation->total_credit * $conversion_rate;
                    }
                    $datasets['week-'.$week]['total_earning'] = $total_earnings;
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
                        'total_earning' => 0,
                        'total_debit' => 0,
                        'total_credit' => 0
                    ];
                    $mutations = clone $query_builder_mutations;
                    foreach($mutations->whereRaw("DATE_FORMAT(mutation_date,'%m-%Y') like ?",[$date_from->format('m-Y')])->cursor() as $mutation) {
                        $conversion_rate = $mutation->currency."_".$currency;
                        $conversion_rate = ($mutation->$conversion_rate ?? 1);
                        $total_earnings += $mutation->total_earning * $conversion_rate;
                        $datasets[$date_from->format('m-Y')]['total_debit'] += $mutation->total_debit * $conversion_rate;
                        $datasets[$date_from->format('m-Y')]['total_credit'] += $mutation->total_credit * $conversion_rate;
                    }
                    $datasets[$date_from->format('m-Y')]['total_earning'] = $total_earnings;
                    unset($mutations);
                }
                break;
        }
        return array_values($datasets);
    }

    public function mutationStatisticByLabel($currency, $params = []) {
        if(!in_array($currency,['usd','cny','idr'])) $currency = 'usd';
        $query_builder = FinanceLabel::whereNull('deleted_at');
        foreach($params as $key => $val) {
            if(isset($val)) {
                switch($key) {
                    case 'name':
                        $query_builder->where('name','like',"%{$val}%");
                    break;
                    default:
                        $query_builder->where($key,$val);
                    break;
                }
            }
        }
        $data = [];
        foreach($query_builder->cursor() as $label) {
            $debit = $label->mutations()->totalDebit($currency);
            $credit = $label->mutations()->totalCredit($currency);
            $data[] = [
                'label' => $label,
                'currency' => $currency,
                'debit' => $debit,
                'credit' => $credit,
                'total' => $debit+$credit
            ];
        }
        return $data;
    }

}
