<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\ProjectLabel;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateTaskParams extends FormRequest
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
            'project_id' => $this->project_id == null ? null : trim(strip_tags($this->project_id)),
            'project_label_id' => $this->project_label_id == null ? null : trim(strip_tags($this->project_label_id)),
            'user_id' => $this->user_id == null ? null : trim(strip_tags($this->user_id)),
            'status' => $this->status == null ? null : array_map(function($val){return trim(strip_tags($val));},$this->status),
            'priority' => $this->priority == null ? null : trim(strip_tags($this->priority)),
            'name' => $this->name == null ? null : trim(strip_tags($this->name))
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
            'project_id' => ['nullable','integer',Rule::exists(Project::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'project_label_id' => ['nullable','integer',Rule::exists(ProjectLabel::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'due_date' => ['nullable','date'],
            'user_id' => ['nullable','integer',Rule::exists(User::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'status.*' => ['nullable','in:todo,inprogress,done'],
            'priority' => ['nullable','in:low,medium,high'],
            'name' => ['nullable','string','max:255']
        ];
    }
}
