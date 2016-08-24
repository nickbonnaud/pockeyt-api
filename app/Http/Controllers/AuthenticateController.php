<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\User;
use App\Account;
use GuzzleHttp\Client;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Http\Requests;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthenticateController extends Controller
{

    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }

    public function register(Request $request){
        $newuser= $request->all();
        $password=Hash::make($request->input('password'));
 
        $newuser['password'] = $password;
        $user = User::create($newuser);


        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json(compact('token'));
    }

    public function facebook(Request $request) {
        $token = $request->input('token');
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('GET', 'https://graph.facebook.com/me', [
                'query' => ['access_token' => $token]
            ]);
        } catch (RequestException $e) {
            // echo Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                return $e->getResponse();
            }
        }
        return $response;
    }
}
