<?php

namespace App\Http\Controllers;

use JWTAuth;
use JWTFactory;
use App\User;
use Response;
use Validator;
use App\Account;
use GuzzleHttp\Client;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Http\Requests;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthenticateController extends Controller
{

    public function authenticate(Request $request)
    {
        if($request->has('fbID')) {
            $fbID = 1910100379110426580;
            $dbUser = User::where('fbID', '=', $fbID)->first();
            if (!$token=JWTAuth::fromUser($dbUser)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
            return response()->json(compact('token'));
        } else {
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
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'unique:users'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return $errors->toJson();
        } else {

            $newuser= $request->all();
            $password=Hash::make($request->input('password'));
            $email = $request->input('email');
     
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
    }

    public function facebook(Request $request) {
        $token = $request->input('token');
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com']);

        try {
            $response = $client->request('GET', '/me', [
                'query' => ['fields' => 'name, email', 'access_token' => $token, ]
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return $e->getResponse();
            }
        }

        $data = json_decode($response->getBody());

        $userName = $data->name;
        if($data->email) {
            $userEmail = $data->email;
        }
        $userfbID = $data->id;

        $fbIDCheck = array('fbID' => $userfbID);
        $validator = Validator::make($fbIDCheck, [
            'fbID' => 'unique:users'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return $errors->toJson();
        } else {

            $res = $client->request('GET', "/$userfbID/picture", [
                'query' => ['type' => 'large', 'redirect' =>'false']
            ]);

            $photoData = json_decode($res->getBody());
            if($photoData->data->is_silhouette === false) {
                $userPhoto = $photoData->data->url;
            }

            $user = new User;
            $user->name = $userName;
            if($userEmail) {
                $user->email = $userEmail;
            }
            if($userPhoto) {
                $user->photo_path = $userPhoto;
            }
            $user->fbID = $userfbID;
            $user->save();

            $dbUser = User::where('fbID', '=', $userfbID)->first();
            if (!$token=JWTAuth::fromUser($dbUser)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
            return response()->json(compact('token'));
        }
    }
}
