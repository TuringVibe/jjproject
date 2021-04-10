<?php

namespace App\Http\Requests;

use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateMilestone extends FormRequest
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
            'name' => trim(strip_tags($this->name)),
            'status' => trim(strip_tags($this->status)),
            'cost' => $this->cost == null ? null : trim(strip_tags($this->cost)),
            'description' => $this->description == null ? null : trim(strip_tags($this->description))
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
            'id' => ['nullable','integer',Rule::exists(Milestone::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'project_id' => ['required','integer',Rule::exists(Project::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'name' => ['required','string','max:255'],
            'status' => ['required','in:incomplete,complete'],
            'cost' => ['nullable','integer'],
            'description' => ['nullable', 'string', 'max:500']
        ];
    }
}
