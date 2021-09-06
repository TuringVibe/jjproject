<?php

namespace App\Http\Requests;

use App\Models\FinanceLabel;
use App\Models\FinanceMutation;
use App\Models\Project;
use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateFinanceMutation extends FormRequest
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
            'mutation_date' => trim(strip_tags($this->mutation_date)),
            'name' => trim(strip_tags($this->name)),
            'currency' => trim(strip_tags($this->currency)),
            'nominal' => trim(strip_tags($this->nominal)),
            'mode' => trim(strip_tags($this->mode)),
            'from_wallet_id' => $this->from_wallet_id == null ? null : trim(strip_tags($this->from_wallet_id)),
            'to_wallet_id' => $this->to_wallet_id == null ? null : trim(strip_tags($this->to_wallet_id)),
            'project_id' => $this->project_id == null ? null : trim(strip_tags($this->project_id)),
            'finance_label_ids.*' => $this->finance_label_ids == null ? null : array_map(function($val){ return trim(strip_tags($val)); },$this->finance_label_ids),
            'notes' => $this->notes == null ? null : trim(strip_tags($this->notes)),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validation = [
            'id' => ['nullable','integer',Rule::exists(FinanceMutation::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'mutation_date' => ['required','date'],
            'name' => ['required','string','max:100'],
            'currency' => ['required','in:usd,cny,idr'],
            'nominal' => ['required','numeric'],
            'mode' => ['required','in:debit,credit,transfer'],
            'from_wallet_id' => ["bail","required_if:mode,transfer","nullable","integer",Rule::exists(Wallet::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'to_wallet_id' => ["bail","required_if:mode,transfer","nullable","integer",Rule::exists(Wallet::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            }),"different:from_wallet_id"],
            'project_id' => ['nullable','integer',Rule::exists(Project::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'finance_label_ids.*' => ['nullable','integer',Rule::exists(FinanceLabel::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'notes' => ['nullable','string','max:255']
        ];
        if($this->id !== null) {
            $validation["mode"] = ['required','in:debit,credit'];
        }
        return $validation;
    }
}
