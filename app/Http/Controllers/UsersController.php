<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use App\User;
use App\Photo;
use App\Http\Requests;
use App\Http\Requests\AddUserPhotoRequest;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->middleware('jwt.auth', []);
    }

    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        $customerId = $user->customer_id;
        if(isset($customerId)) {
            $result = \Braintree_Customer::find($customerId);
            $user['cardLast4'] = $result->creditCards[0]->last4;
            $user['cardImageUrl'] = $result->creditCards[0]->imageUrl;
            $user['cardToken'] = $result->creditCards[0]->token;

            return response()->json(compact('user'));
        } else {
            return response()->json(compact('user'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAuthenticatedUser(Request $request)
    {
        $authUser = JWTAuth::parseToken()->authenticate();
        if($authUser) {
            $dbUser = User::findOrFail($authUser->id);
            $dbUser->update($request->all());
            return response()->json(compact('dbUser'));
        } else {
            return response('Unauthorized', 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyAuthenticatedUser($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if($user) {
            User::destroy($user->id);
            return response('Success', 200);
        } else {
            return response('Unauthorized', 403);
        }
    }

    public function postPhoto(AddUserPhotoRequest $request) {
        $authUser = JWTAuth::parseToken()->authenticate();
        if($authUser) {
            $dbUser = User::findOrFail($authUser->id);
            $oldPhoto = $dbUser->photo_id;

            if(isset($oldPhoto)) {
                $photo = Photo::where('id', '=', $oldPhoto);
                $photo->delete();
            }

            $file = $request->file('file');
            $photo = Photo::fromForm($file);
            $photo->save();

            $dbUser['photo_path'] = url($photo->path);
            $dbUser['photo_id'] = $photo->id;
            $dbUser->save();

            return response('Success', 200);
        }
    }

     public function deletePhoto(DeleteUserPhotoRequest $request) {
        $authUser = JWTAuth::parseToken()->authenticate();
        $dbUser = User::findOrFail($user->id);
        $photo = $dbUser->{$type};
        $profile->{$type}()->dissociate()->save();
        $photo->delete();
        return back();
    }
}
