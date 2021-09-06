<?php
namespace App\Services;

use App\Models\FinanceMutation;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletService {

    public function get($params = []) {
        $query_builder = Wallet::with("mutations")->whereNull('deleted_at');
        foreach($params as $field => $val) {
            if(isset($val)) {
                if($field == 'name') {
                    $query_builder->where($field,'like',"%{$val}%");
                } else {
                    $query_builder->where($field,$val);
                }
            }
        }
        $query_builder = $query_builder->orderBy('created_at','desc')
            ->select('id','name','default_currency');

        $wallets = collect();
        foreach($query_builder->cursor() as $wallet) {
            $total_idr = $wallet->mutations()->totalDebit("idr") + $wallet->mutations()->totalCredit("idr");
            $total_usd = $wallet->mutations()->totalDebit("usd") + $wallet->mutations()->totalCredit("usd");
            $total_cny = $wallet->mutations()->totalDebit("cny") + $wallet->mutations()->totalCredit("cny");
            $wallets->push([
                'id' => $wallet->id,
                'name' => $wallet->name,
                'default_currency' => $wallet->default_currency,
                'total_idr' => $total_idr,
                'total_usd' => $total_usd,
                'total_cny' => $total_cny
            ]);
        }

        return $wallets;
    }

    public function detail($id) {
        $wallet = Wallet::find($id);
        $wallet->total_balance = $wallet->mutations()->totalDebit($wallet->default_currency) + $wallet->mutations()->totalCredit($wallet->default_currency);
        return $wallet;
    }

    public function save($attr) {
        $user_id = Auth::user()->id;
        $wallet = null;
        if(isset($attr['id'])) {
            if(array_key_exists("initial_balance",$attr)) unset($attr["initial_balance"]);
            $wallet = Wallet::find($attr['id']);
            $attr['updated_by'] = $user_id;
            $wallet->fill($attr);
            $wallet->save();
        } else {
            if(!isset($attr["initial_balance"])) $initial_balance = 0;
            else $initial_balance = $attr["initial_balance"];
            if(array_key_exists("initial_balance",$attr)) unset($attr["initial_balance"]);
            $attr['created_by'] = $user_id;
            $attr['updated_by'] = $user_id;
            try{
                DB::transaction(function () use(&$wallet, $attr, $initial_balance){
                    $wallet = Wallet::create($attr);
                    $finance_mutation_attr = [
                        'mutation_date' => Carbon::now()->toDateString(),
                        'name' => "Initial balance of ".$wallet->name,
                        'currency' => $wallet->default_currency,
                        'nominal' => $initial_balance,
                        'mode' => "debit",
                        'from_wallet_id' => $wallet->id,
                        'notes' => 'Initial balance defined when creating the wallet'
                    ];
                    (new FinanceMutationService())->save($finance_mutation_attr);
                });
            } catch(\Exception $e) {
                throw new \Exception(__('response.save_failed'), 500);
            }
        }
        return $wallet;
    }

    public function delete($id) {
        $logged_in_user_id = Auth::user()->id;
        $now = Carbon::now();
        $wallet = Wallet::find($id);
        $wallet->deleted_by = $logged_in_user_id;
        $wallet->deleted_at = $now;
        try{
            DB::transaction(function () use(&$wallet, $logged_in_user_id){
                $wallet->mutations()->update([
                    "wallet_id" => null,
                    "updated_by" => $logged_in_user_id
                ]);
                $wallet->save();
            });
            return true;
        }catch(\Exception $e) {
            throw new \Exception(__('response.delete_failed'), 500);
        }
    }

}
