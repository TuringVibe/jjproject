<?php

namespace App\Http\Requests;

use App\Models\FinanceLabel;
use App\Models\Project;
use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateFinanceMutationParams extends FormRequest
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
            'from_date' => $this->from_date == null ? null : trim(strip_tags($this->from_date)),
            'to_date' => $this->to_date == null ? null : trim(strip_tags($this->to_date)),
            'wallet_id' => $this->wallet_id == null ? null : trim(strip_tags($this->wallet_id)),
            'mode' => $this->mode == null ? null : trim(strip_tags($this->mode)),
            'label_id' => $this->label_id == null ? null : trim(strip_tags($this->label_id)),
            'project_id' => $this->project_id == null ? null : trim(strip_tags($this->project_id)),
            'name' => $this->name == null ? null : trim(strip_tags($this->name))
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
            'from_date' => ['nullable','date'],
            'to_date' => ['nullable','date','after_or_equal:from_date'],
            'wallet_id' => ['nullable','integer',function($attribute, $value, $fail){
                if($value != 0) {
                    $label = Wallet::whereNull('deleted_at')->where('id',$value)->first();
                    if(!$label) $fail("{$attribute} must be exists");
                }
            }],
            'mode' => ['nullable','in:debit,credit'],
            'label_id' => ['nullable','integer',function($attribute, $value, $fail){
                if($value != 0) {
                    $label = FinanceLabel::whereNull('deleted_at')->where('id',$value)->first();
                    if(!$label) $fail("{$attribute} must be exists");
                }
            }],
            'project_id' => ['nullable','integer',function($attribute, $value, $fail){
                if($value != 0) {
                    $project = Project::whereNull('deleted_at')->where('id',$value)->first();
                    if(!$project) $fail("{$attribute} must be exists");
                }
            }],
            'name' => ['nullable','string','max:100']
        ];
    }
}
