<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateEventParams extends FormRequest
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
            'start' => trim(strip_tags($this->start)),
            'end' => trim(strip_tags($this->end))
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
            'start' => ['required','date'],
            'end' => ['required','date','after_or_equal:start'],
            'tz' => ['nullable','timezone']
        ];
    }
}
