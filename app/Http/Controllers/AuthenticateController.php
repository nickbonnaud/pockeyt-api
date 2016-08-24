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
        
         $validator = Validator::make($request->all(), [
            'email' => 'unique:users'
        ]);

        if ($validator->fails()) {
            return withErrors($validator)
                        ->withInput();
        }

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

    public function facebook(Request $request) {
        $token = $request->input('token');
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('GET', 'https://graph.facebook.com/me', [
                'query' => ['fields' => 'name, email, picture', 'access_token' => $token, ]
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
        if($data->picture->data->is_silhouette === false) {
            $userPhoto = $data->picture->data->url;
        }

        $user = new User;
        $user->name = $userName;
        if($userEmail) {
            $user->email = $userEmail;
        }
        if($userEmail) {
            $user->photo_path = $userPhoto;
        }
        $user->fbID = $userfbID;

        $user->save();
        $credentials = ['email' => $userEmail, 'id' => $userfbID ];

        $payload = JWTFactory::make($credentials);
        $token = JWTAuth::encode($payload);
        
        return Response::json(['token' => $token->get()]);
    }
}
