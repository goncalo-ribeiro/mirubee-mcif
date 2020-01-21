<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReadingThreePhase extends Model
{
    protected $table = 'readings_three_phase';

    protected $fillable = ['ip', 'time', 'v1', 'v2', 'v3', 'vt', 'i1', 'i2', 'i3', 'it', 'p1', 'p2', 'p3', 'pt', 'a1', 'a2', 'a3', 'at',
    'r1', 'r2', 'r3', 'rt', 'q1', 'q2', 'q3', 'qt', 'f1', 'f2', 'f3', 'ft', 'e1', 'e2', 'e3', 'et', 'o1', 'o2', 'o3', 'ot', 'ps'];

    public function device()
    {
        return $this->belongsTo('App\Device');
    }
}
