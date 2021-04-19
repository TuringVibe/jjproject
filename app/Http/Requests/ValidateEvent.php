<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateEvent extends FormRequest
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
            'startdatetime' => trim(strip_tags($this->startdatetime)),
            'enddatetime' => trim(strip_tags($this->enddatetime)),
            'name' => trim(strip_tags($this->name)),
            'repeat' => trim(strip_tags($this->repeat))
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
            'id' => ['nullable','integer',Rule::exists(Event::class, 'id')->where(function($query){
                $query->whereNull('deleted_at');
            })],
            'startdatetime' => ['required','date_format:Y-m-d H:i:s'],
            'enddatetime' => ['required','date_format:Y-m-d H:i:s', 'after_or_equal:startdatetime'],
            'name' => ['required','string','max:255'],
            'repeat' => ['required','in:once,daily,weekly,biweekly,monthly,yearly']
        ];
    }
}
