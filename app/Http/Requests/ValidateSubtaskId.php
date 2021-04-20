<?php

namespace App\Http\Requests;

use App\Models\Subtask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateSubtaskId extends FormRequest
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
            'id' => ['required','integer',Rule::exists(Subtask::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })]
        ];
    }
}