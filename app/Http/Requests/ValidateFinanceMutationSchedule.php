<?php

namespace App\Http\Requests;

use App\Models\FinanceLabel;
use App\Models\FinanceMutationSchedule;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateFinanceMutationSchedule extends FormRequest
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
            'next_mutation_date' => trim(strip_tags($this->next_mutation_date)),
            'name' => trim(strip_tags($this->name)),
            'nominal' => trim(strip_tags($this->nominal)),
            'mode' => trim(strip_tags($this->mode)),
            'project_id' => $this->project_id == null ? null : trim(strip_tags($this->project_id)),
            'attached_label_ids' => $this->attached_label_ids == null ? null : array_map(function($val){return trim(strip_tags($val));},$this->attached_label_ids),
            'repeat' => trim(strip_tags($this->repeat)),
            'notes' => $this->notes == null ? null : trim(strip_tags($this->notes))
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
            'id' => ['nullable','integer',Rule::exists(FinanceMutationSchedule::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'next_mutation_date' => ['required','date','after:today'],
            'name' => ['required','string','max:100'],
            'nominal' => ['required','integer'],
            'mode' => ['required','in:debit,credit'],
            'project_id' => ['nullable','integer',Rule::exists(Project::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'attached_label_ids.*' => ['nullable','integer',Rule::exists(FinanceLabel::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'repeat' => ['required','in:daily,weekly,monthly,yearly'],
            'notes' => ['nullable','string','max:255']
        ];
    }
}
