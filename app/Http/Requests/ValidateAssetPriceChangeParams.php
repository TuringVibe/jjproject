<?php

namespace App\Http\Requests;

use App\Models\FinanceAsset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateAssetPriceChangeParams extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'finance_asset_id' => ['nullable','integer',Rule::exists(FinanceAsset::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })]
        ];
    }
}
