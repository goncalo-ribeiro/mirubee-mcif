<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //o argumento $nickname devia ter outro nome, pois caso a função não encontre o utilizador com o nickname tenta faze-lo com email
    //devia ser chamado de $loginCredential
    public function findForPassport($name) {
        $user = $this->where('name', $name)->first();
        if (is_null($user)) {
            $user = $this->where('email', $nickname)->first();
        }
        return $user;
    }
}
