<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Users extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $mfa = $this->mfaMethod;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'notifications' => $this->notifications,
            'mfaMethods' => ['authenticated' => false, 
                // 'authenticated' => $mfa->authenticated, 
                'email' => (is_null($mfa->email_code)) ? false : true,
                'email_activated' => $mfa->email_activated,
                'google' => (is_null($mfa->google_code)) ? false : true,
                'u2f' => (is_null($mfa->u2f_code)) ? false : true,
                'sqrl' => (is_null($mfa->sqrl_code)) ? false : true,
            ]

        ];
    }
}
