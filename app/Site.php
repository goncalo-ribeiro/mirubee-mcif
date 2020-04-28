<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function devices()
    {
        return $this->hasMany('App\Device');
    }

    public function tariff()
    {
        return $this->belongsTo('App\Tariff');
    }

    public function readingsThreePhase()
    {
        return $this->hasManyThrough('App\ReadingThreePhase', 'App\Device');
    }
}
