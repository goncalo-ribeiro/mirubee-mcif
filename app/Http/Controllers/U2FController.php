<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use WebAuthn\WebAuthn;
use App\Login;
use Illuminate\Support\Facades\Log;
use Session;
use Illuminate\Http\Request;
use App\U2FAuthenticationMethod;
use App\Http\Resources\Users as UserResource;

class U2FController extends Controller
{   
    public $requireResidentKey, $WebAuthn;

    public function __construct()
    {
        //session_start();
        // read get argument and post body
        //$fn = $_GET['fn'];
        //$this->requireResidentKey = false;
        /*
        $post = trim(file_get_contents('php://input'));
        if ($post) {
            $post = json_decode($post);
        }*/
    
        // Formats
        $formats = array();
        $formats[] = 'android-key';   
        //$formats[] = 'android-safetynet';   
        $formats[] = 'fido-u2f';   
        //$formats[] = 'none';   
        $formats[] = 'packed';   
        //$formats[] = 'tpm';
        
        // new Instance of the server library.
        // make sure that $rpId is the domain name.
        $this->WebAuthn = new WebAuthn('mirubee', 'mirubee.test', $formats);
        // add root certificates to validate new registrati
        $this->WebAuthn->addRootCertificates('rootCertificates/solo.pem');
        $this->WebAuthn->addRootCertificates('rootCertificates/yubico.pem');
        $this->WebAuthn->addRootCertificates('rootCertificates/hypersecu.pem');
        $this->WebAuthn->addRootCertificates('rootCertificates/globalSign.pem');
        $this->WebAuthn->addRootCertificates('rootCertificates/googleHardware.pem');
    }

    public function getCreateArgs()
    {
        $user = \Auth::user();
        $createArgs = $this->WebAuthn->getCreateArgs($user->id, $user->name, $user->name, 20);

        // print(json_encode($createArgs));
        // save challange to session. you have to deliver it to processGet later.
        Session::put('challenge', $this->WebAuthn->getChallenge());
        return response()->json($createArgs, 200);
    }

    public function processCreate(Request $request) {
        $loggedOnUser = \Auth::user();
        $mfaMethod = $loggedOnUser->mfaMethod;

        $clientDataJSON = base64_decode($request->input('clientDataJSON'));
        $attestationObject = base64_decode($request->input('attestationObject'));
        $challenge = Session::get('challenge');
        // processCreate returns data to be stored for future logins. in this example we store it in the php session.
        // Normaly you have to store the data in a database connected with the user name.
        $data = $this->WebAuthn->processCreate($clientDataJSON, $attestationObject, $challenge);

        $u2fAuthenticationMethod = new U2FAuthenticationMethod;

        $u2fAuthenticationMethod->credentialId = $data->credentialId;
        $u2fAuthenticationMethod->credentialPublicKey = $data->credentialPublicKey;
        $u2fAuthenticationMethod->certificate = $data->certificate;
        $u2fAuthenticationMethod->signatureCounter = $data->signatureCounter;
        $u2fAuthenticationMethod->AAGUID = $data->AAGUID;
        $u2fAuthenticationMethod->user()->associate($loggedOnUser);
        $u2fAuthenticationMethod->save();

        $mfaMethod->u2f_code = 1;
        $mfaMethod->save();

        $return = new \stdClass();
        $return->success = true;
        $return->msg = 'Registration Success.';
        $return->user = new UserResource(\Auth::user());
        return response()->json($return, 200);
    }

    public function getGetArgs(Request $request) {
        $ids = array();
        $loggedOnUser = \Auth::user();
        $userU2FAuthMethod = $loggedOnUser->u2fAuthenticationMethod;
        Log::debug('user id = '. $loggedOnUser->id);

        // if (is_a($userU2FAuthMethods, 'Illuminate\Database\Eloquent\Collection')) {
        //     foreach ($userU2FAuthMethods as $reg) {
        //         $ids[] = $reg->credentialId;
        //     }
        // }
        $ids[] = $userU2FAuthMethod->credentialId;
        if (count($ids) === 0 || is_null($userU2FAuthMethod)) {
            throw new \Exception('no registrations in session.');
        }

        $getArgs = $this->WebAuthn->getGetArgs($ids);
        Session::put('challenge', $this->WebAuthn->getChallenge());
        return response()->json($getArgs, 200);
        // save challange to session. you have to deliver it to processGet later.
    }

    public function processGet(Request $request) {
        $clientDataJSON = base64_decode($request->input('clientDataJSON'));
        $authenticatorData = base64_decode($request->input('authenticatorData'));
        $signature = base64_decode($request->input('signature'));
        $id = base64_decode($request->input('id'));
        $challenge = Session::get('challenge');
        $credentialPublicKey = null;

        // looking up correspondending public key of the credential id
        // you should also validate that only ids of the given user name are taken for the login.
        $loggedOnUser = \Auth::user();
        $userU2FAuthMethod = $loggedOnUser->u2fAuthenticationMethod;

        $credentialPublicKey = $userU2FAuthMethod->credentialPublicKey;

        if ($credentialPublicKey === null) {
            throw new \Exception('Public Key for credential ID not found!');
        }
        // process the get request. throws WebAuthnException if it fails
        $this->WebAuthn->processGet($clientDataJSON, $authenticatorData, $signature, $credentialPublicKey, $challenge);

        // authenticated from now on
        $mfaMethod = $loggedOnUser->mfaMethod;
        $mfaMethod->authenticated = true;
        $mfaMethod->save();
        
        $return = new \stdClass();
        $return->success = true;
        $return->message = 'authenticated through u2f';
        
        if($request->remember){
            if(is_null($mfaMethod->remember_mfa_token)){
                $mfaMethod->remember_mfa_token = \Hash::make(\Str::random(20));
                $mfaMethod->save();
            }
            $return->remember_token = $mfaMethod->remember_mfa_token;
        }
        return response()->json($return, 200);
    }

    public function deactivate()
    {
        $user = \Auth::user();
        $user->fido_code = "";
        $user->authenticated = 0;

        $userFidoAuthMethods = $user->fidoAuthenticationMethods;
        if (is_a($userFidoAuthMethods, 'Illuminate\Database\Eloquent\Collection')) {
            foreach ($userFidoAuthMethods as $method) {
                $method->delete();
            }
        }
        $user->save();
        $message = ['message_success' => 'Fido Authentication Removed'];

        return redirect()->route('settings')->with($message);
    }
}
