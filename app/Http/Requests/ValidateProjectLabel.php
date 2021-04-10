<?php

namespace App\Http\Requests;

use App\Models\ProjectLabel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateProjectLabel extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['nullable', 'integer', Rule::exists(ProjectLabel::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'name' => ['required', 'string', 'max:100', Rule::unique(ProjectLabel::class, 'name')->where(function($query){
                return $query->whereNull('deleted_at');
            })->ignore(ProjectLabel::find($this->id))],
            'color' => ['required','string', 'size:7', 'starts_with:#']
        ];
    }
}
