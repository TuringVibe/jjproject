<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateUser extends FormRequest
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
            'id' => ['nullable', 'integer', Rule::exists(User::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'email' => ['required','email','max:255', Rule::unique(User::class, 'email')->where(function($query){
                return $query->whereNull('deleted_at');
            })->ignore(User::find($this->id))],
            'role' => ['required','in:user,admin'],
            'password' => ['bail','required_without:id','nullable','max:100','confirmed'],
            'firstname' => ['required','string','max:255'],
            'lastname' => ['nullable','string','max:255'],
            'img' => ['nullable','image'],
            'phone' => ['nullable','max:15'],
            'address' => ['nullable','max:255'],
            'city' => ['nullable','max:255'],
            'state' => ['nullable','max:255'],
            'country' => ['nullable','max:255'],
            'zip_code' => ['nullable','max:255'],
        ];
    }
}
