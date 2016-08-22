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

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAuthenticatedUser(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if($user) {
            $new_user = $user->update($request->all());
            return $new_user;
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
            $file = $request->file('file');
            $photo = Photo::fromForm($file);
            $photo->save();

            $dbUser = User::findOrFail($authUser->id);
            $dbUser['photo_path'] = $photo->path;
            $dbUser->save();

            return response('ok');
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
