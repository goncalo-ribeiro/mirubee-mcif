<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'type',
        'condition',
        'threshold',
        'threshold2',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
