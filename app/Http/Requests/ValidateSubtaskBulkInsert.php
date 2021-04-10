<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateSubtaskBulkInsert extends FormRequest
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
        $name = $this->name;
        if(is_array($name)) {
            $name = array_map(function($val){return trim(strip_tags($val));},$name);
        } else $name = trim(strip_tags($name));
        $this->merge([
            'task_id' => trim(strip_tags($this->task_id)),
            'name' => $name,
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
        $name = 'name';
        if(is_array($this->name)) $name = 'name.*';
        return [
            'task_id' => ['required','integer',Rule::exists(Task::class,'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'is_done' => ['required','boolean'],
            $name => ['required','string','max:500']
        ];
    }
}
