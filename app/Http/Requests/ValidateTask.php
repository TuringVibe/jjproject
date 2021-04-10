<?php

namespace App\Http\Requests;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateTask extends FormRequest
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
            'project_id' => trim(strip_tags($this->project_id)),
            'milestone_id' => $this->milestone_id == null ? null : trim(strip_tags($this->milestone_id)),
            'name' => trim(strip_tags($this->name)),
            'status' => trim(strip_tags($this->status)),
            'priority' => trim(strip_tags($this->priority)),
            'order' => $this->order == null ? null : trim(strip_tags($this->order)),
            'description' => $this->description == null ? null : trim(strip_tags($this->description)),
            'due_date' => $this->due_date == null ? null : trim(strip_tags($this->due_date)),
            'user_ids.*' => $this->user_ids == null ? null : array_map(function($val){return trim(strip_tags($val));}, $this->user_ids)
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
            'id' => ['nullable','integer',Rule::exists(Task::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'project_id' => ['required','integer',Rule::exists(Project::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'milestone_id' => ['nullable','integer',Rule::exists(Milestone::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'name' => ['required','string','max:255'],
            'status' => ['required','in:todo,inprogress,done'],
            'priority' => ['required','in:low,medium,high'],
            'order' => ['nullable','integer'],
            'description' => ['nullable','string','max:65535'],
            'due_date' => ['nullable','date'],
            'user_ids.*' => ['nullable','integer',Rule::exists(User::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })]
        ];
    }
}
