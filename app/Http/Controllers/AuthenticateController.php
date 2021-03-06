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

    public function __construct()
    {
        parent::__construct();
    }

    public function authenticate(Request $request)
    {
        if($request->has('fbID')) {
            $fbID = $request->input('fbID');
            $user = User::where('fbID', '=', $fbID)->first();
            if (!$token=JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
            $user['token'] = $token;
            return response()->json(compact('user'));
        } else {
            // grab credentials from the request
            $credentials = $request->only('email', 'password');
            $user = User::where('email', '=', $request->input('email'))->first();

            try {
                // attempt to verify the credentials and create a token for the user
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 401);
                }
            } catch (JWTException $e) {
                // something went wrong whilst attempting to encode the token
                return response()->json(['error' => 'could_not_create_token'], 500);
            }

            $user['token'] = $token;
            return response()->json(compact('user'));
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
            $password = Hash::make($request->input('password'));
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
            $user['token'] = $token;
            return response()->json(compact('user'));
        }
    }

    public function update(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();

        $dbUser = User::findOrFail($user->id);
        $user = $request->all();
        if (Hash::check($request->input('password'), $dbUser->password)) {
            $newPassword = $request->input('passwordNew');
            if(isset($newPassword)) {
                $password = Hash::make($newPassword);
                $user['password'] = $password;
                $dbUser->update($user);
                $credentials = $request->only('email');
                $credentials['password'] = $request->passwordNew;
            } else {
                $password =  Hash::make($request->input('password'));
                $user['password'] = $password;
                $dbUser->update($user);
                $credentials = $request->only('email', 'password');
            }
        } else {
            return response()->json('wrong password', 401);
        }
       

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $dbUser['token'] = $token;
        return response()->json(compact('dbUser'));
    }

    public function facebook(Request $request) {
        $token = $request->input('token');
        $client = new Client();

        try {
            $response = $client->get('https://graph.facebook.com/me', [
                'query' => ['fields' => 'first_name, last_name, email', 'access_token' => $token, ]
            ]);
        } catch(RequestException $e) {
            if ($e->hasResponse()) {
                return $e->getResponse();
            }
        }


        // $client = new \GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com']);
        // try {
        //     $response = $client->request('GET', '/me', [
        //         'query' => ['fields' => 'first_name, last_name, email', 'access_token' => $token, ]
        //     ]);
        // } catch (RequestException $e) {
        //     if ($e->hasResponse()) {
        //         return $e->getResponse();
        //     }
        // }

        $data = json_decode($response->getBody());

        $userFirstName = $data->first_name;
        $userLastName = $data->last_name;
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

            $res = $client->get("https://graph.facebook.com/$userfbID/picture", [
                'query' => ['type' => 'large', 'redirect' =>'false']
            ]);

            // $res = $client->request('GET', "/$userfbID/picture", [
            //     'query' => ['type' => 'large', 'redirect' =>'false']
            // ]);

            $photoData = json_decode($res->getBody());
            if($photoData->data->is_silhouette === false) {
                $userPhoto = $photoData->data->url;
            }

            $user = new User;
            $user->first_name = $userFirstName;
            $user->last_name = $userLastName;
            if($userEmail) {
                $user->email = $userEmail;
            }
            if($userPhoto) {
                $user->photo_path = $userPhoto;
            }
            $user->fbID = $userfbID;
            $user->save();

            $user = User::where('fbID', '=', $userfbID)->first();

            if (!$token=JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
            $user['token'] = $token;
            return response()->json(compact('user'));
        }
    }
}
