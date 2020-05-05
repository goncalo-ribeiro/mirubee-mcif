<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use App\MfaMethod;

use App\Http\Resources\Users as UserResource;

class UserControllerApi extends Controller
{
    public function store(Request $request)
    {
        $valid = validator($request->only('email', 'name', 'password'), [
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($valid->fails()) {
            $jsonError=response()->json($valid->errors()->all(), 400);
            return $jsonError;
        }
        $data = request()->only('email','name','password');
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        $user->email_verified_at = now();
        $user->remember_token = \Str::random(10);
        $user->save();

        $mfaMethod = new MfaMethod;
        $mfaMethod->user_id = $user->id;
        $mfaMethod->save();
        $jsonSuccess=response()->json('Registered', 200);
        return $jsonSuccess;
    }

    public function userByEmail($email){
        $user = User::where('email', $email)->first();
        if(is_null($user->email_verified_at)){
            return response()->json(['msg' => 'Email not confirmed'], 403);
            //return response()->json(['msg'=>'Account has not been verified'], 403); //Forbidden email has not been verified ask if needs a new Email to confirm
        }
        return new UserResource($user);
    }
}
