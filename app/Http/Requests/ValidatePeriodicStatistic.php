<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePeriodicStatistic extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function prepareForValidation()
    {
        $date_range = explode(" - ",$this->date_range);
        $this->merge([
            'date_from' => trim(strip_tags($date_range[0])),
            'date_to' => trim(strip_tags($date_range[1])),
            'periode' => trim(strip_tags($this->periode)),
            'currency' => $this->currency == null ? null : trim(strip_tags($this->currency))
        ]);
    }

    public function rules() {
        return [
            'date_from' => ['required','date'],
            'date_to' => ['required','date','after_or_equal:date_from'],
            'periode' => ['required','in:weekly,monthly'],
            'currency' => ['nullable','in:usd,cny,idr']
        ];
    }

}
