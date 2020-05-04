<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\EmailActivation as EmailActivationNotification; 
use App\Http\Resources\Users as UserResource;

class MfaMethodController extends Controller
{
    public function sendActivationEmail(){
        $user = Auth::user();
        $mfaMethod = $user->mfaMethod;

        if($mfaMethod->email_activated){
            return response()->json(['errors' => ['user' => ["this user has already established ownership of its email"]]], 400);
        }

        if(!is_null($mfaMethod->email_code)){
            return response()->json(['errors' => ['user' => ["this user is already using its email as method of mfa"]]], 400);
        }

        $code = \Str::random(20);
        $mfaMethod->email_temp_code = \Hash::make($code);
        $mfaMethod->save();

        $user->notify(new EmailActivationNotification($code));

        return response()->json(['message' => 'activation email sent successfully'], 200);
    }

    public function activateEmail(Request $request){
        $user = Auth::user();
        $mfaMethod = $user->mfaMethod;
        if(\Hash::check($request->code, $mfaMethod->email_temp_code))
        {
            $mfaMethod->email_code = \Hash::make(\Str::random(20));
            $mfaMethod->email_activated = true;
            $mfaMethod->authenticated = true;
            $mfaMethod->save();
            return response()->json(['message' => 'email authentication set up', 'user' => new UserResource(Auth::user())], 200);
        }
        return response()->json(['errors' => ['email code' => ["the sent code doesn't match our records"]]], 400);
    }

    public function enableEmail(){
        $user = Auth::user();
        $mfaMethod = $user->mfaMethod;
        $mfaMethod->email_code = \Hash::make(\Str::random(20));
        $mfaMethod->save();
        return response()->json(['message' => 'email authentication method enabled', 'user' => new UserResource(Auth::user())], 200);
    }

    public function disableEmail(){
        $user = Auth::user();
        $mfaMethod = $user->mfaMethod;
        $mfaMethod->email_code = null;
        $mfaMethod->save();
        return response()->json(['message' => 'email authentication method disabled', 'user' => new UserResource(Auth::user())], 200);
    }
}
