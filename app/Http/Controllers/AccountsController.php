<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddAccountPhotoRequest;
use App\Http\Requests\DeleteAccountPhotoRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Photo;
use App\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;

class AccountsController extends Controller {

    /**
     * Create a new AccountsController instance
     */
    public function __construct() {
        $this->middleware('jwt.auth', []);
    }

    /**************************
     * Resource actions
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  AccountRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountRequest $request) {
        $user = JWTAuth::parseToken()->authenticate();
        if(!is_null($this->user->account))
            return response('Account already exists');

            $account = $this->user->publishAccount(
                new Account($request->all())
            );
        return response('success', 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountRequest $request, $id) {
        $user = JWTAuth::parseToken()->authenticate();
        /** @var Profile $account */
        $account = Account::findOrFail($id);
        $account->update($request->all());

        return $account;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteAccountRequest $request, $id) {
        $user = JWTAuth::parseToken()->authenticate();
        /** @var Profile $account */
        $account = Account::findOrFail($id);
        
        if($account) {
            $account->delete();
            return response('success', 200);
        } else {
            return response('unauthorized', 403);
        }
    }

    /**************************
     * Other actions
     */

    public function postPhotos(AddAccountPhotoRequest $request, $account_id) {
        $file = $request->file('photo');
        $photo = Photo::fromForm($file);
        $photo->save();
        Account::findOrFail($account_id)->{$request->get('type')}()->associate($photo)->save();
        return response('ok');
    }

    public function deletePhotos(DeleteAccountPhotoRequest $request, $account_id) {
        /** @var Profile $account */
        $account = Account::findOrFail($account_id);
        $type = $request->get('type');
        $photo = $account->{$type};
        $account->{$type}()->dissociate()->save();
        $photo->delete();
        return back();
    }
}
