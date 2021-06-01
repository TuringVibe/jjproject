<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateFinanceAsset;
use App\Http\Requests\ValidateFinanceAssetId;
use App\Http\Requests\ValidateFinanceAssetParams;
use App\Services\FinanceAssetService;

class FinanceAssetController extends Controller
{
    private $finance_asset_service;

    public function __construct(FinanceAssetService $finance_asset_service)
    {
        $this->finance_asset_service = $finance_asset_service;
    }

    public function list() {
        $this->config['title'] = "FINANCE ASSET";
        $this->config['active'] = "finance-assets.list";
        $this->config['navs'] = [
            [
                'label' => 'Finance Asset'
            ]
        ];
        return view('pages.finance-asset-list', $this->config);
    }

    public function data(ValidateFinanceAssetParams $request) {
        $params['name'] = $request->name;
        $result = $this->finance_asset_service->get($params);
        return response()->json($result->toArray());
    }

    public function edit(ValidateFinanceAssetId $request) {
        $result = $this->finance_asset_service->detail($request->id);
        return $result;
    }

    public function save(ValidateFinanceAsset $request) {
        $result = $this->finance_asset_service->save($request->validated());
        return $result;
    }

    public function delete(ValidateFinanceAssetId $request) {
        $result = $this->finance_asset_service->delete($request->id);
        return $result;
    }
}
