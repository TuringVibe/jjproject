<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateUserParams extends FormRequest
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
        $this->merge(
            [
                'name' => $this->name == null ? null : trim(strip_tags($this->name)),
                'role' => $this->role == null ? null : trim(strip_tags($this->role))
            ]
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['nullable','string','max:510'],
            'role' => ['nullable','in:user,admin']
        ];
    }
}
