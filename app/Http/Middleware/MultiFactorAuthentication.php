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
        
        if(!is_null($mfa->google_code) || !is_null($mfa->u2f_code) || !is_null($mfa->email_code) || !is_null($mfa->sms_code)){
            Log::debug('mfa enabled! checking if user is authenticated');
            if(!$mfa->authenticated){
                Log::debug('mfa not authenticated, declining request');
                return response()->json(['message' => "mfa Unauthenticated."], 401);
            }
        }
        Log::debug('mfa not enabled! forwarding request');

        // if($user->session != \Session::getId()){
        //     return redirect()->route('auth-pin');
        // }

        return $next($request);
    }
}
