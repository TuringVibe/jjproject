<?php

namespace App\Http\Requests;

use App\Models\FinanceMutationSchedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateFinanceMutationScheduleId extends FormRequest
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
            'id' => trim(strip_tags($this->id))
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
            'id' => ['required','integer',Rule::exists(FinanceMutationSchedule::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })]
        ];
    }
}