<?php

namespace App\Http\Requests;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateSubtask extends FormRequest
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
            'name' => trim(strip_tags($this->name)),
            'is_done' => $this->is_done == "true" ? true : false
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
            'id' => ['nullable','integer',Rule::exists(Subtask::class,'id')->where(function($query) {
                $query->whereNull('deleted_at');
            })],
            'task_id' => ['required','integer',Rule::exists(Task::class, 'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'name' => ['required','string','max:500'],
            'is_done' => ['required','boolean']
        ];
    }
}
