<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

define('YOUR_SERVER_URL', env("APP_URL", "http://127.0.0.1:8000/"));  //deve funcionar para nos os dois desde que o url esteja definido no .env
// Check "oauth_clients" table for next 2 values:
define('CLIENT_ID', '2');

define('CLIENT_SECRET', DB::table('oauth_clients')->where('id', '2')->value('secret'));

class LoginControllerApi extends Controller
{
    public function login(Request $request)
    {
        try{
            $request->request->add([
                "grant_type" => "password",
                "username" => $request->email,
                "password" => $request->password,
                "client_id"     => CLIENT_ID,
                "client_secret" => CLIENT_SECRET,
            ]);

            $tokenRequest = $request->create(
                YOUR_SERVER_URL.'/oauth/token',
                'post'
            
            );

            $response = Route::dispatch($tokenRequest);

            $errorCode = $response->getStatusCode();
            if ($errorCode=='200') {
                return json_decode((string) $response->getContent(), true);    
            }else {
                return response()->json(['msg'=>'User credentials are invalid'], $errorCode);
            }
        }
        catch(Exception $e){
            dd($e->getMessage());
        }
    }
}
