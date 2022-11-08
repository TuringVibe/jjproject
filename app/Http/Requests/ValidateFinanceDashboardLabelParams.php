<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateFinanceDashboardLabelParams extends FormRequest
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
            'currency' => $this->currency == null ? null : trim(strip_tags($this->currency)),
            'name' => $this->name == null || trim(strip_tags($this->name)) === "" ? null : trim(strip_tags($this->name))
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
            'currency' => ['nullable','in:usd,cny,idr'],
            'name' => ['nullable','string','max:100']
        ];
    }
}
