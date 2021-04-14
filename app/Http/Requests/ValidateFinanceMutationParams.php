<?php

namespace App\Http\Requests;

use App\Models\FinanceLabel;
use App\Models\Project;
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
        $date_range = $this->date_range == null ? null : explode(' - ',$this->date_range);
        $this->merge([
            'date_from' => $date_range == null ? null : trim(strip_tags($date_range[0])),
            'date_to' => $date_range == null ? null : trim(strip_tags($date_range[1])),
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
            'date_from' => ['nullable','date'],
            'date_to' => ['nullable','date','after_or_equal:due_date_from'],
            'mode' => ['nullable','in:debit,credit'],
            'label_id' => ['nullable','integer',Rule::exists(FinanceLabel::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
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
