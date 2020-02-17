<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Tariff extends FormRequest
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
            'contracted_power' => 'required | numeric',
            'daily_power_price' => 'required | numeric',
            'tax' => 'required | numeric',
            'tariff_type' => 'required | string | in:simple,bi-hourly,tri-hourly',

            'price_simple' => 'required_if:tariff_type,simple | nullable | numeric',
            'price_off_peak_hours' => 'required_unless:tariff_type,simple |nullable| numeric',
            'price_outside_off_peak_hours' => 'required_if:tariff_type,bi-hourly |nullable| numeric',
            'price_peak_hours' => 'required_if:tariff_type,tri-hourly |nullable| numeric',
            'price_full_time_hours' => 'required_if:tariff_type,tri-hourly |nullable| numeric',
        ];
    }

    public function attributes()
    {
        return [
            'price_simple' => 'consumption price',
            'price_off_peak_hours' => 'price of off peak hours',
            'price_peak_hours' => 'price of peak hours',
            'price_full_time_hours' => 'price of full time hours',
        ];
    }

    public function messages()
    {
        return [
            'price_off_peak_hours.required_unless' => 'The price of off peak hours is required when the tariff type is not simple',
        ];
    }
}
