<?php

namespace App\Http\Requests;

use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateWallet extends FormRequest
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
            'id' => $this->id == null ? null : trim(strip_tags($this->id)),
            'name' => trim(strip_tags($this->name)),
            'default_currency' => trim(strip_tags($this->default_currency)),
            'initial_balance' => $this->initial_balance == null ? null : trim(strip_tags($this->initial_balance))
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "id" => ["nullable","integer",Rule::exists(Wallet::class,"id")->where(function($query){
                $query->whereNull("deleted_at");
            })],
            "name" => ["required","string","max:100"],
            "default_currency" => ["required","in:usd,cny,idr"],
            "initial_balance" => ["nullable","numeric"]
        ];
    }
}
