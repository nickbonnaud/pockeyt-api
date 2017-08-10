<?php

namespace App\Http\Controllers;

use App\User;
use App\Account;
use App\Profile;
use Illuminate\Http\Request;
use Validator;
use Crypt;
use SplashPayments;
use App\Http\Requests;
use App\Http\Requests\AccountRequest;
use App\Http\Requests\AccountOwnerRequest;
use App\Http\Requests\AccountBankRequest;
use App\Http\Requests\UpdateAccountIndividualRequest;
use App\Http\Requests\UpdateAccountBusinessRequest;
use App\Http\Requests\UpdateAccountPayRequest;
use App\Http\Requests\EditAccountRequest;
use App\Http\Controllers\Controller;

class AccountsController extends Controller
{
    
    public function __construct() {
        $this->middleware('auth', ['except' => ['postStatus']]);

        parent::__construct();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setBusinessInfo(AccountRequest $request)
    {
        

        if(!is_null($this->user->profile->account))
            return redirect()->route('profiles.show', ['profiles' => Crypt::encrypt($this->user->profile->id)]);

        $account = $this->user->profile->publish(
            new Account($request->except('annualCCSales'))
        );

        $account->annualCCSales = preg_replace("/[^0-9]/","",$request->annualCCSales);
        $account->status = 'review';
        $account->save();

        return redirect()->route('accounts.createOwner');
    }

    public function createOwnerInfo()
    {
       return view('accounts.create_owner');
    }

    public function setOwnerInfo (AccountOwnerRequest $request) {
        $account = $this->user->profile->account;
        $account->update($request->except('ssn', 'ownership'));
        $account->ssn = Crypt::encrypt($request->ssn);
        $account->ownership = $request->ownership * 100;
        $account->save();
        return redirect()->route('accounts.createBank');
    }

    public function createBankInfo()
    {
       return view('accounts.create_bank');
    }

    public function setBankInfo(AccountBankRequest $request) {
        $account = $this->user->profile->account;
        $account->method = $request->method;
        $account->accountNumber = Crypt::encrypt($request->accountNumber);
        $account->routing = Crypt::encrypt($request->routing);
        $account->save();
        $account = $this->shortenSensitive($account);
        flash()->success('Account Info Submitted!', 'Awaiting Pockeyt Approval');
        return redirect()->route('accounts.edit', ['accounts' => Crypt::encrypt($account->id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EditAccountRequest $request, $id)
    {
        $account = Account::findOrFail(Crypt::decrypt($id));
        $account = $this->shortenSensitive($account);
        return view('accounts.edit', compact('account'));
    }

    public function changePersonal(UpdateAccountIndividualRequest $request, $id)
    {
        $account = Account::findOrFail($id);

        $account->update($request->except('ssn', 'ownership'));
        if(!starts_with($request->ssn, 'X')) {
            $account->ssn = Crypt::encrypt($request->ssn);
        }
        $account->ownership = $request->ownership * 100;
        $account->status = 'review';
        $account->save();
        $account = $this->shortenSensitive($account);
        return redirect()->route('accounts.edit', ['accounts' => Crypt::encrypt($account->id)]);
    }

    public function changeBusiness(UpdateAccountBusinessRequest $request, $id)
    {
        dd($request->all());
        $account = Account::findOrFail($id);
        $account->update($request->except('annualCCSales'));

        $account->annualCCSales = preg_replace("/[^0-9]/","",$request->annualCCSales);
        $account->status = 'review';
        $account->save();
        $account = $this->shortenSensitive($account);
        return redirect()->route('accounts.edit', ['accounts' => Crypt::encrypt($account->id)]);
    }

    public function changePay(UpdateAccountPayRequest $request, $id)
    {
        $account = Account::findOrFail($id);
        if(!starts_with($request->accountNumber, 'X')) {
            $account->accountNumber = Crypt::encrypt($request->accountNumber);
        }
        if(!starts_with($request->routing, 'X')) {
            $account->routing = Crypt::encrypt($request->routing);
        }
        $account->method = $request->method;
        $account->status = 'review';
        $account->save();
        $account = $this->shortenSensitive($account);
        return redirect()->route('accounts.edit', ['accounts' => Crypt::encrypt($account->id)]);
    }


    public function getConnections() {
        return view('accounts.connections');
    }

    public function shortenSensitive($account) {
        $account->ssn = substr(Crypt::decrypt($account->ssn), -4);
        $account->accountNumber = substr(Crypt::decrypt($account->accountNumber), -4);
        $account->routing = substr(Crypt::decrypt($account->routing), -4);
        return $account;
    }

    public function postApprove(Request $request) {
        $account = Account::findOrFail($request->accountId);
        $mcc = $request->mcc;
        $account->status = 'pending';
        $account->save();
        $this->sendToSplash($account, $mcc);
        return redirect()->back();
    }

    public function sendToSplash($account, $mcc) {
        SplashPayments\Utilities\Config::setTestMode(true);
        SplashPayments\Utilities\Config::setApiKey(env('SPLASH_KEY'));
        $object = new SplashPayments\merchants(
            array (
                'new' => 0,
                'established' => date_format(date_create($account->established), 'Ymd'),
                'annualCCSales' => $account->annualCCSales * 100,
                'mcc' => $mcc,
                'status' => 1,
                'tcVersion' => 1,
                'entity' => array(
                    'type' => $account->businessType,
                    'name' => $account->legalBizName,
                    'address1' => $account->bizStreetAdress,
                    'city' => $account->bizCity,
                    'state' => $account->bizState,
                    'zip' => $account->bizZip,
                    'country' => "USA",
                    'phone' => preg_replace("/[^0-9]/","", $account->phone),
                    'email' => $account->accountEmail,
                    'ein' => preg_replace("/[^0-9]/","", $account->bizTaxId),
                    'website' => $account->profile->website,
                    'accounts' => array(
                        array(
                            'primary' => 1,
                            'account' => array(
                                'method' => $account->method,
                                'number' => Crypt::decrypt($account->accountNumber),
                                'routing' => Crypt::decrypt($account->routing)
                            )
                        )
                    )
                ),
                'members' => array(
                    array(
                        'first' => $account->accountUserFirst,
                        'last' => $account->accountUserLast,
                        'dob' => date_format(date_create($account->dateOfBirth), 'Ymd'),
                        'ownership' => $account->ownership,
                        'email' => $account->ownerEmail,
                        'ssn' => preg_replace("/[^0-9]/","", Crypt::decrypt($account->ssn)),
                        'primary' => 1
                    )
                )
            )
        );

        try {
            $object->create();
        }
        catch (SplashPayments\Exceptions\Base $e) {

        }
        $response = $object->getResponse();
        $account->splashId = $response[0]->id;
        return $account->save();
    }

    public function postStatus(Request $request) {
        $business = json_decode($request->getContent());
        $account = Account::where('splashId', '=', $business->id)->first();
        if ($business->status == 2) {
            $account->status = "active";
        } elseif ($business->status == 4) {
            $account->status = 'denied';
        } else {
            $account->status = 'pending';
        }
        $account->save();
    }
}
