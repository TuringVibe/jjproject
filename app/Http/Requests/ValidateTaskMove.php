<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateTaskMove extends FormRequest
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
            'project_id' => trim(strip_tags($this->project_id)),
            'task_id' => trim(strip_tags($this->task_id)),
            'dest_status' => trim(strip_tags($this->dest_status)),
            'dest_order' => trim(strip_tags($this->dest_order))
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
            'project_id' => ['required','integer',Rule::exists(Project::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'task_id' => ['required','integer',Rule::exists(Task::class,'id')->where(function($query){
                $query->whereNull('deleted_at')
                    ->where('project_id',$this->project_id);
            })],
            'dest_status' => ['required','in:todo,inprogress,done'],
            'dest_order' => ['required','integer']
        ];
    }
}
