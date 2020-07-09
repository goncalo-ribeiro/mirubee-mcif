<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\EmailActivation as EmailActivationNotification; 
use App\Notifications\EmailAuthentication as EmailAuthenticationNotification; 
use App\Http\Resources\Users as UserResource;
use App\Classes\GoogleAuthenticator;
use DestruidorPT\LaravelSQRLAuth\App\Http\Controllers as SQRL;

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
            $mfaMethod->save();
            return response()->json(['message' => 'email authentication set up', 'user' => new UserResource(Auth::user())], 200);
        }
        return response()->json(['errors' => ['email code' => ["the sent code doesn't match our records"]]], 400);
    }

    public function enableEmail(){
        $user = Auth::user();
        $mfaMethod = $user->mfaMethod;

        if(!$mfaMethod->email_activated){
            return response()->json(['errors' => ['user' => ["this user hasn't established ownership of its email yet"]]], 400);
        }

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

    public function sendAuthenticationEmail(){
        $user = Auth::user();
        $mfaMethod = $user->mfaMethod;

        if(is_null($mfaMethod->email_code)){
            return response()->json(['errors' => ['user' => ["this user hasn't enabled its email as method of mfa yet"]]], 400);
        }

        $code = \Str::random(20);
        $mfaMethod->email_code = \Hash::make($code);
        $mfaMethod->save();

        $user->notify(new EmailAuthenticationNotification($code));

        return response()->json(['message' => 'authentication email sent successfully'], 200);
    }

    public function authenticateThroughEmail(Request $request){
        $user = Auth::user();
        $mfaMethod = $user->mfaMethod;
        if(\Hash::check($request->code, $mfaMethod->email_code))
        {
            if($request->remember){
                $mfaMethod->authenticated = true;
                $mfaMethod->save();
                if(is_null($mfaMethod->remember_mfa_token)){
                    $mfaMethod->remember_mfa_token = \Hash::make(\Str::random(20));
                    $mfaMethod->save();
                }
                return response()->json(['message' => 'authenticated through email', 'remember_token' => $mfaMethod->remember_mfa_token], 200);        

            }
            return response()->json(['message' => 'authenticated through email', 'remember_token' => null], 200);
        }
        return response()->json(['errors' => ['email code' => ["the sent code doesn't match our records"]]], 400);

    }

    public function activateGoogle()
    {
        $user = Auth::user();
        $mfaMethod = $user->mfaMethod;

        if(!is_null($mfaMethod->email_code)){
            return response()->json(['errors' => ['user' => ["this user is already using its email as method of mfa"]]], 400);
        }

        $ga = new GoogleAuthenticator();
        $secret = $ga->createSecret();
        $qrCode = $ga->getQRCodeGoogleUrl('Mcif-Mirubee', $secret);
        return response()->json(['message' => 'google secret created', 'secret' => $secret, 'qrCode' => $qrCode], 200);        
    }

    public function enableGoogle(Request $request)
    {
        $ga = new GoogleAuthenticator();
        if($ga->verifyCode($request->secret, $request->code, 2))
        {
            $user = Auth::user();
            $mfaMethod = $user->mfaMethod;
            $mfaMethod->google_code = encrypt($request->secret);
            $mfaMethod->save();
            return response()->json(['message' => 'google authenticator set up', 'user' => new UserResource(Auth::user())], 200);        
        }
        $secret = $request->secret;
        $qrCode = $ga->getQRCodeGoogleUrl('Squirrel', $secret);
        return response()->json(['errors' => ['google code' => ['wrong one time code']]], 400);
    }

    public function disableGoogle()
    {
        $user = Auth::user();
        $mfaMethod = $user->mfaMethod;
        
        $mfaMethod->google_code = null;
        $mfaMethod->save();
        return response()->json(['message' => 'google authenticator method disabled', 'user' => new UserResource(Auth::user())], 200);        
    }

    function authenticateThroughGoogle(Request $request)
    {
        $ga = new GoogleAuthenticator();
        $user = Auth::user();
        $mfaMethod = $user->mfaMethod;
        if($ga->verifyCode(decrypt($mfaMethod->google_code), $request->code, 2))
        {
            $mfaMethod->authenticated = true;
            $mfaMethod->save();
            if($request->remember){
                if(is_null($mfaMethod->remember_mfa_token)){
                    $mfaMethod->remember_mfa_token = \Hash::make(\Str::random(20));
                    $mfaMethod->save();
                }
                return response()->json(['message' => 'authenticated through google authenticator', 'remember_token' => $mfaMethod->remember_mfa_token], 200);        

            }
            return response()->json(['message' => 'authenticated through google authenticator', 'remember_token' => null], 200);
        }
        return response()->json(['errors' => ['google code' => ["wrong one time code"]]], 400);
    }

    public function disableU2F()
    {
        $user = Auth::user();
        $mfaMethod = $user->mfaMethod;
        
        $mfaMethod->u2f_code = null;
        $mfaMethod->save();

        $userU2FAuthMethod = $user->u2fAuthenticationMethod;
        $userU2FAuthMethod->delete();

        return response()->json(['message' => 'u2f authenticator method disabled', 'user' => new UserResource(Auth::user())], 200);        
    }

    public function getSqrlNonce()
    {
        return response()->json(['nonce' => SQRL\SQRL\SQRLController::getNewAuthNonce()], 200);        
    }

    public function loginSqrl(Request $request){
        if(isset($_GET["nut"]) && !empty($_GET["nut"])) { // Check if the nut exist or if it's past on URL https://site.test?nut=<nonce value>
            $object = SQRL\SQRL\SQRLController::getUserByOriginalNonceIfCanBeAuthenticated($_GET["nut"]); //Get the user by the original nonce
            if(isset($object)) { //Will be null if the nonce expired or is invalid
                if($object instanceof Sqrl_pubkey) { // This only happen when no SQRL Client is associated to the user, then Sqrl_pubkey from SQRL CLient is returned
                    //new user
                    return view('LaravelSQRLAuthExemples.newsqrl');//View for the user to create account or associate to one already created
                } else if($object > 0) { //This happen when SQRL Client is associated to a user, so the value is number and is the id of the user
                    Auth::loginUsingId($object); //This is for authenticate the user with that id
                }
            }
        }
    }

}
