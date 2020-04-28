<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    protected $fillable = [
        'contracted_power',
        'daily_power_price',
        'tax',
        'tariff_type',
        'price_simple',
        'price_off_peak_hours',
        'price_outside_off_peak_hours',
        'price_peak_hours',
        'price_full_time_hours',
        'starting_time_off_peak_hours',
        'starting_time_outside_off_peak_hours',
        'starting_time_peak_hours',
        'starting_time_full_time_hours',
    ];

    public function site()
    {
        return $this->hasOne('App\Site');
    }
}
