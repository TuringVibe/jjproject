<?php

namespace App\Http\Requests;

use App\Models\FinanceAsset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateFinanceAsset extends FormRequest
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

    public function prepareForValidation() {
        $this->merge([
            'id' => $this->sanitizeInput($this->id),
            'name' => $this->sanitizeInput($this->name),
            'qty' => $this->sanitizeInput($this->qty),
            'unit' => $this->sanitizeInput($this->unit),
            'buy_datetime' => $this->sanitizeInput($this->buy_datetime),
            'currency' => $this->sanitizeInput($this->currency),
            'buy_price_per_unit' => $this->sanitizeInput($this->buy_price_per_unit),
        ]);
    }

    private function sanitizeInput($val) {
        return $val == null ? null : trim(strip_tags($val));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['nullable','integer',Rule::exists(FinanceAsset::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'name' => ['required','string','max:100'],
            'qty' => ['required','numeric'],
            'unit' => ['nullable','string','max:10'],
            'buy_datetime' => ['required','date_format:Y-m-d H:i:s'],
            'currency' => ['required','in:usd,cny,idr'],
            'buy_price_per_unit' => ['required','numeric']
        ];
    }
}
