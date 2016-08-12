<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddAccountPhotoRequest;
use App\Http\Requests\DeleteAccountPhotoRequest;
use App\Http\Requests\EditAccountRequest;
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
        $this->middleware('auth', []);
        $this->middleware('auth:admin', ['only' => ['index']]);

        parent::__construct();
    }

    /**************************
     * Resource actions
     */

    public function index() {
        $accounts = Account::with(['owner', 'user_photo'])->latest()->get();

        return view('accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        if(!is_null($this->user->account))
            return redirect()->route('accounts.show', ['accounts' => $this->user->account->id]);
        return view('accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AccountRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountRequest $request) {
        if(!is_null($this->user->account))
            return redirect()->route('accounts.show', ['accounts' => $this->user->account->id]);

        $account = $this->user->publish(
            new Account($request->all())
        );

        return redirect(account_path($account));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $account = (!is_null($this->user) && $this->user->is_admin) ? Account::find($id) : Account::find($id);

        return view('accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EditAccountRequest $request, $id) {
        $account = Account::findOrFail($id);
        return view('accounts.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountRequest $request, $id) {
        /** @var Profile $account */
        $account = Account::findOrFail($id);
        $account>update($request->all());

        return redirect()->route('accounts.show', ['accounts' => $id]);
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
