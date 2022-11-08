<?php
namespace App\Services;

use App\Models\FinanceAsset;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FinanceAssetService {

    public function statistic($currency) {
        if(!in_array($currency,['usd','cny','idr'])) $currency = 'usd';
        $query_builder = FinanceAsset::whereNull('deleted_at');
    }

    public function get($params = []) {
        $query_builder = FinanceAsset::whereNull('deleted_at');
        foreach($params as $field => $val) {
            if(isset($val)) {
                if($field == 'name') {
                    $query_builder->where($field,'like',"%{$val}%");
                } else {
                    $query_builder->where($field,$val);
                }
            }
        }
        $query_builder->orderBy('created_at','desc');
        $finance_assets = collect();
        foreach($query_builder->cursor() as $finance_asset) {
            $convert_to_usd = $finance_asset->{$finance_asset->currency."_usd"} ?? 1;
            $convert_to_cny = $finance_asset->{$finance_asset->currency."_cny"} ?? 1;
            $convert_to_idr = $finance_asset->{$finance_asset->currency."_idr"} ?? 1;
            $finance_assets->push([
                'id' => $finance_asset->id,
                'name' => $finance_asset->name,
                'qty' => $finance_asset->qty,
                'unit' => $finance_asset->unit,
                'buy_datetime' => $finance_asset->buy_datetime,
                'usd_unit' => $finance_asset->buy_price_per_unit * $convert_to_usd,
                'usd_total' => $finance_asset->buy_price_per_unit * $finance_asset->qty * $convert_to_usd,
                'cny_unit' => $finance_asset->buy_price_per_unit * $convert_to_cny,
                'cny_total' => $finance_asset->buy_price_per_unit * $finance_asset->qty * $convert_to_cny,
                'idr_unit' => $finance_asset->buy_price_per_unit * $convert_to_idr,
                'idr_total' => $finance_asset->buy_price_per_unit * $finance_asset->qty * $convert_to_idr,
            ]);
        }

        return $finance_assets;
    }

    public function detail($id) {
        $finance_asset = FinanceAsset::find($id);
        return $finance_asset;
    }

    public function save($attr) {
        try{
            $finance_asset = null;
            DB::transaction(function () use(&$finance_asset, $attr){
                if(isset($attr['id'])) {
                    $finance_asset = FinanceAsset::find($attr['id']);
                    $attr['updated_by'] = Auth::user()->id;
                    $finance_asset->fill($attr);
                    $finance_asset->save();
                } else {
                    $this->currencyConversion($attr);
                    $attr['created_by'] = Auth::user()->id;
                    $attr['updated_by'] = $attr['created_by'];
                    $finance_asset = FinanceAsset::create($attr);
                }
            });
            return $finance_asset;
        } catch(\Exception $e) {
            throw new \Exception(__('response.save_failed'), 500);
        }
    }

    public function delete($id) {
        try {
            DB::transaction(function() use($id){
                $user_id = Auth::user()->id;
                $now = Carbon::now();
                $finance_asset = FinanceAsset::find($id);
                $finance_asset->price_changes()->update(['deleted_by' => $user_id, 'deleted_at' => $now]);
                $finance_asset->deleted_by = $user_id;
                $finance_asset->deleted_at = $now;
                $finance_asset->save();
            });
            return true;
        } catch (\Exception $th) {
            throw new Exception(__("response.delete_failed"), 500);
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

        $last_rate = FinanceAsset::whereNull('deleted_at')
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
