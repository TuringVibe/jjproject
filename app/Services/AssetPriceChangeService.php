<?php
namespace App\Services;

use App\Models\AssetPriceChange;
use App\Models\FinanceAsset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AssetPriceChangeService {

    public function get($params = []) {
        $query_builder = AssetPriceChange::whereNull('deleted_at');
        foreach($params as $field => $val) {
            if(isset($val)) {
                switch($field) {
                    default:
                        $query_builder->where($field,$val);
                    break;
                }
            }
        }
        $query_builder->orderBy('change_datetime','desc');
        $asset_price_changes = collect();
        foreach($query_builder->cursor() as $asset_price_change) {
            $convert_to_usd = $asset_price_change->{$asset_price_change->currency."_usd"} ?? 1;
            $convert_to_cny = $asset_price_change->{$asset_price_change->currency."_cny"} ?? 1;
            $convert_to_idr = $asset_price_change->{$asset_price_change->currency."_idr"} ?? 1;
            $asset_price_changes->push([
                'id' => $asset_price_change->id,
                'change_datetime' => $asset_price_change->change_datetime,
                'usd_unit' => $asset_price_change->price_per_unit * $convert_to_usd,
                'usd_total' => $asset_price_change->price_per_unit * $asset_price_change->finance_asset->qty * $convert_to_usd,
                'cny_unit' => $asset_price_change->price_per_unit * $convert_to_cny,
                'cny_total' => $asset_price_change->price_per_unit * $asset_price_change->finance_asset->qty * $convert_to_cny,
                'idr_unit' => $asset_price_change->price_per_unit * $convert_to_idr,
                'idr_total' => $asset_price_change->price_per_unit * $asset_price_change->finance_asset->qty * $convert_to_idr,
            ]);
        }

        return $asset_price_changes;
    }

    public function detail($id) {
        $asset_price_change = AssetPriceChange::whereNull('deleted_at')
            ->where('id',$id)->first();
        return $asset_price_change;
    }

    public function save($attr) {
        try{
            $asset_price_change = null;
            DB::transaction(function () use(&$asset_price_change, $attr){
                $user_id = Auth::user()->id;
                if(isset($attr['id'])) {
                    $asset_price_change = AssetPriceChange::find($attr['id']);
                    $attr['updated_by'] = $user_id;
                    $asset_price_change->fill($attr);
                    $asset_price_change->save();
                } else {
                    $this->currencyConversion($attr);
                    $finance_asset = FinanceAsset::find($attr['finance_asset_id']);
                    $attr['created_by'] = $user_id;
                    $attr['updated_by'] = $user_id;
                    $asset_price_change = new AssetPriceChange($attr);
                    $finance_asset->price_changes()->save($asset_price_change);
                }
            });
            return $asset_price_change;
        } catch(\Exception $e) {
            throw new \Exception(__('response.save_failed'), 500);
        }
    }

    public function delete($id) {
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        $asset_price_change = AssetPriceChange::find($id);
        $asset_price_change->deleted_by = $user_id;
        $asset_price_change->deleted_at = $now;
        return $asset_price_change->save();
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

        $last_rate = AssetPriceChange::whereNull('deleted_at')
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
