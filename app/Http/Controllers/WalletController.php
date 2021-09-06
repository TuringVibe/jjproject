<?php
namespace App\Http\Controllers;

use App\Http\Requests\ValidateWallet;
use App\Http\Requests\ValidateWalletId;
use App\Http\Requests\ValidateWalletParams;
use App\Services\WalletService;

class WalletController extends Controller {

    private $wallet_service;

    public function __construct(WalletService $wallet_service)
    {
        $this->wallet_service = $wallet_service;
    }

    public function list() {
        $this->config['title'] = "WALLETS";
        $this->config['active'] = "wallets.list";
        $this->config['navs'] = [
            [
                'label' => 'Wallets'
            ]
        ];
        return view('pages.wallet-list', $this->config);

    }

    public function data(ValidateWalletParams $request) {
        $params['name'] = $request->name;
        $result = $this->wallet_service->get($params);
        echo json_encode($result);
    }

    public function detail(ValidateWalletId $request) {
        $result = $this->wallet_service->detail($request->id);
        return $result;
    }

    public function save(ValidateWallet $request) {
        $result = $this->wallet_service->save($request->validated());
        return $result;
    }

    public function delete(ValidateWalletId $request) {
        $result = $this->wallet_service->delete($request->id);
        return $result;
    }
}
