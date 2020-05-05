<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\Log;

class MultiFactorAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        Log::debug($user);

        if(is_null($user)){
            return response()->json(['message' => "basic Unauthenticated."], 401);
        }

        $mfa = $user->mfaMethod;
        Log::debug($mfa);
        
        if(!is_null($mfa->google_code) || !is_null($mfa->fido_code) || !is_null($mfa->email_code) || !is_null($mfa->sms_code)){
            if(!$mfa->authenticated){
                return response()->json(['message' => "mfa Unauthenticated."], 401);
            }
        }

        // if($user->session != \Session::getId()){
        //     return redirect()->route('auth-pin');
        // }

        return $next($request);
    }
}
