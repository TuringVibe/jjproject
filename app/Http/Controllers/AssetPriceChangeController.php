<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateAssetPriceChange;
use App\Http\Requests\ValidateAssetPriceChangeId;
use App\Http\Requests\ValidateAssetPriceChangeParams;
use App\Services\AssetPriceChangeService;

class AssetPriceChangeController extends Controller
{
    private $asset_price_change_service;

    public function __construct(AssetPriceChangeService $asset_price_change_service)
    {
        $this->asset_price_change_service = $asset_price_change_service;
    }

    public function data(ValidateAssetPriceChangeParams $request) {
        $params['finance_asset_id'] = $request->finance_asset_id;
        $result = $this->asset_price_change_service->get($params);
        echo json_encode($result->toArray());
    }

    public function edit(ValidateAssetPriceChangeId $request) {
        $result = $this->asset_price_change_service->detail($request->id);
        return $result;
    }

    public function save(ValidateAssetPriceChange $request) {
        $result = $this->asset_price_change_service->save($request->validated());
        if($result) {
            return [
                'status' => true,
                'message' => __('response.save_succeed'),
                'data' => $result
            ];
        }
        return [
            'status' => false,
            'message' => __('response.save_failed'),
        ];
    }

    public function delete(ValidateAssetPriceChangeId $request) {
        $result = $this->asset_price_change_service->delete($request->id);
        if($result) {
            return [
                'status' => true,
                'message' => __('response.delete_succeed'),
                'data' => $result
            ];
        }
        return [
            'status' => false,
            'message' => __('response.delete_failed'),
        ];
    }

}
