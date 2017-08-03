<?php

namespace App\Http\Controllers;

use App\User;
use App\Photo;
use Crypt;
use Illuminate\Http\Request;
use App\Http\Requests\EditBusinessUserRequest;
use App\Http\Requests\UpdateBusinessUserRequest;
use App\Http\Requests\AddUserPhotoRequest;
use App\Http\Requests\BusinessUserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\ShowUserRequest;
use Validator;
use App\Http\Requests;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class BusinessUsersController extends Controller
{
    
    public function __construct() {
        $this->middleware('auth', []);
        $this->middleware('auth:admin', ['only' => ['index', 'create']]);

        parent::__construct();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShowUserRequest $request, $id)
    {
        $user = User::findOrFail(Crypt::decrypt($id));
        return view('users.show', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBusinessUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return redirect()->route('users.show', ['users' => Crypt::encrypt($user->id)]);
    }

    public function postPhotos(AddUserPhotoRequest $request, $id) {
        $user = User::findOrFail($id);
        $oldPhoto = $user->photo_path;

        if(isset($oldPhoto)) {
            $photo = Photo::where('path', '=', $oldPhoto);
            $photo->delete();
        }

        $file = $request->file('photo');
        $photo = Photo::fromForm($file);
        $photo->save();

        $user['photo_path'] = url($photo->path);
        $user->save();

        return response('ok');
    }

    public function changePassword(UpdatePasswordRequest $request, $id) {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:9|max:72|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%=@&?]).*$/',
            'old_password' => 'required',
            'password_confirm' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.show', ['users' => Crypt::encrypt($this->user->id)])
                ->withErrors($validator);
        } else {
            $user = User::findOrFail($id);
            $old_password = $request->input('old_password');
            $new_password = $request->input('new_password');

            if (Hash::check($old_password, $user->getAuthPassword())) {
                $user->password = Hash::make($new_password);

                if ($user->save()) {
                    return redirect()->route('users.show', ['users' => Crypt::encrypt($user->id)]);
                } else {
                    return redirect()->route('users.show', ['users' => Crypt::encrypt($user->id)])
                        ->withErrors("Oops Something went wrong. Try again later");
                }
            } else {
                return redirect()->route('users.show', ['users' => Crypt::encrypt($user->id)])
                    ->withErrors('Incorrect Password');
            }
        }
    }
}
