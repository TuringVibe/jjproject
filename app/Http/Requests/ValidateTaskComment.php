<?php

namespace App\Http\Requests;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateTaskComment extends FormRequest
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
            'task_id' => trim(strip_tags($this->task_id)),
            'user_id' => trim(strip_tags($this->user_id)),
            'comment' => trim(strip_tags($this->comment))
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
            'id' => ['nullable','integer',Rule::exists(TaskComment::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'task_id' => ['required','integer',Rule::exists(Task::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'user_id' => ['required','integer',Rule::exists(User::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'comment' => ['required','string','max:65535']
        ];
    }
}
