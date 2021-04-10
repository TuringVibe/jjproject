<?php

namespace App\Http\Requests;

use App\Models\ProjectLabel;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateProjectParams extends FormRequest
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
            'status' => $this->status == null ? null : trim(strip_tags($this->status)),
            'user_id' => $this->user_id == null ? null : trim(strip_tags($this->user_id)),
            'project_label_id' => $this->project_label_id == null ? null : trim(strip_tags($this->project_label_id)),
            'name' => $this->name == null ? null : trim(strip_tags($this->name)),
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
            'status' => ['nullable','in:notstarted,ongoing,complete,onhold,canceled'],
            'user_id' => ['nullable','integer',Rule::exists(User::class, "id")->where(function($query) {
                $query->whereNull('deleted_at');
            })],
            'project_label_id' => ['nullable','integer',Rule::exists(ProjectLabel::class,"id")->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'name' => ['nullable','string','max:255']
        ];
    }
}
