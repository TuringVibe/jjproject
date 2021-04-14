<?php

namespace App\Http\Requests;

use App\Models\FinanceLabel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateFinanceLabel extends FormRequest
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
            'color' => trim(strip_tags($this->color))
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
            'id' => ['nullable', 'integer', Rule::exists(FinanceLabel::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'name' => ['required', 'string', 'max:100', Rule::unique(FinanceLabel::class, 'name')->where(function($query){
                return $query->whereNull('deleted_at');
            })->ignore(FinanceLabel::find($this->id))],
            'color' => ['required','string', 'size:7', 'starts_with:#']
        ];
    }
}
