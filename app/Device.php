<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    public function readingsThreePhase()
    {
        return $this->hasMany('App\ReadingThreePhase');
    }

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
