<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Alert extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required | string',
            'unit' => 'required | in:voltage,current,apparent power,active power,frequency,power factor',
            'type' => 'required | in:email,website',
            'condition' => 'required | in:bigger than,lesser than,between,equal',
            'threshold' => 'required | numeric',
            'threshold2' => 'required_if:condition,between | nullable | numeric | gt:threshold',
        ];
    }

    public function attributes()
    {
        return [
            'threshold2' => 'second value in the condition',
        ];
    }
}
