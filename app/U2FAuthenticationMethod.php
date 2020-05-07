<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class U2FAuthenticationMethod extends Model
{
    protected $table = 'u2f_authentication_methods';
    
    protected$attributeModifiers = 
        ['credentialId'=> Base64Encoder::class,
        'AAGUID'=> Base64Encoder::class
    ,];

    public function user () {
        return $this->belongsTo('App\User', 'user_id');
    }
}
