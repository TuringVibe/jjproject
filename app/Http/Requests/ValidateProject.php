<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\ProjectLabel;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateProject extends FormRequest
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
            'name' => trim(strip_tags($this->name)),
            'status' => trim(strip_tags($this->status)),
            'budget' => $this->budget == null ? null : trim(strip_tags($this->budget)),
            'startdate' => $this->startdate == null ? null : trim(strip_tags($this->startdate)),
            'enddate' => $this->enddate == null ? null : trim(strip_tags($this->enddate)),
            'description' => $this->description == null ? null : trim(strip_tags($this->description)),
            'user_ids.*' => $this->user_ids == null ? null : array_map(function($val){ return trim(strip_tags($val)); },$this->user_ids),
            'project_label_ids.*' => $this->project_label_ids == null ? null : array_map(function($val){ return trim(strip_tags($val)); },$this->project_label_ids),
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
            'id' => ['nullable','integer',Rule::exists(Project::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'name' => ['required','string','max:255'],
            'status' => ['required','in:notstarted,ongoing,complete,onhold,canceled'],
            'budget' => ['nullable','integer'],
            'startdate' => ['nullable','date'],
            'enddate' => ['nullable','date','after_or_equal:startdate'],
            'description' => ['nullable','string','max:500'],
            'user_ids.*' => ['nullable','integer',Rule::exists(User::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'project_label_ids.*' => ['nullable','integer',Rule::exists(ProjectLabel::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })]
        ];
    }
}
