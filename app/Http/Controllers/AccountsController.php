<?php

namespace App\Http\Controllers;

use App\User;
use App\Account;
use App\Profile;
use Illuminate\Http\Request;
use Validator;
use Crypt;
use App\Http\Requests;
use App\Http\Requests\AccountRequest;
use App\Http\Requests\AccountOwnerRequest;
use App\Http\Requests\AccountBankRequest;
use App\Http\Requests\UpdateAccountIndividualRequest;
use App\Http\Requests\UpdateAccountBusinessRequest;
use App\Http\Requests\UpdateAccountPayRequest;
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
            return redirect()->route('profiles.show', ['profiles' => $this->user->profile->id]);

        $account = $this->user->profile->publish(
            new Account($request->all())
        );

        return redirect()->route('accounts.createOwner');
    }

    public function createOwnerInfo()
    {
       return view('accounts.create_owner');
    }

    public function setOwnerInfo (AccountOwnerRequest $request) {
        $account = $this->user->account;
        $account->update($request->except('ssn'));
        $account->ssn = Crypt::encrypt($request->ssn);
        $account->save();
        return redirect()->route('accounts.createBank');
    }

    public function createBankInfo()
    {
       return view('accounts.create_bank');
    }

    public function setBankInfo(AccountBankRequest $request) {
        $account = $this->user->account;
        $account->method = $request->method;
        $account->account_number = Crypt::encrypt($request->account_number);
        $account->routing = Crypt::encrypt($request->routing);
        $account->save();
        flash()->success('Account Info Submitted!', 'Awaiting Pockeyt Approval');
        return view('accounts.edit', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $account = Account::findOrFail($id);
        return view('accounts.edit', compact('account'));
    }

    public function changePersonal(UpdateAccountIndividualRequest $request, $id)
    {
        $account = Account::findOrFail($id);

        $result = \Braintree_MerchantAccount::update(
            $account->profile_id,
            [
                'individual' => [
                    'firstName' => $request->accountUserFirst,
                    'lastName' => $request->accountUserLast,
                    'email' => $request->accountEmail,
                    'dateOfBirth' => $request->dateOfBirth,
                    'ssn' => $request->last4,
                    'address' => [
                        'streetAddress' => $request->indivStreetAdress,
                        'locality' => $request->indivCity,
                        'region' => $request->indivState,
                        'postalCode' => $request->indivZip
                    ]
                ]
            ]
        );
        if ($result->success) {
            $account->update($request->all());
            return view('accounts.edit', compact('account'));
        } else {
            return view('accounts.edit', compact('account'))
                ->withErrors($result->errors->deepAll());
        }
    }

    public function changeBusiness(UpdateAccountBusinessRequest $request, $id)
    {
        $account = Account::findOrFail($id);

        $result = \Braintree_MerchantAccount::update(
            $account->profile_id,
            [
                'business' => [
                    'legalName' => $request->legalBizName,
                    'taxId' => $request->bizTaxId
                ]
            ]
        );
        if ($result->success) {
            $account->update($request->all());
            return view('accounts.edit', compact('account'));
        } else {
            return view('accounts.edit', compact('account'))
                ->withErrors($result->errors->deepAll());
        }
    }

    public function changePay(UpdateAccountPayRequest $request, $id)
    {
        $account = Account::findOrFail($id);

        $result = \Braintree_MerchantAccount::update(
            $account->profile_id,
            [
                'funding' => [
                    'destination' => \Braintree_MerchantAccount::FUNDING_DESTINATION_BANK,
                    'accountNumber' => $request->accountNumber4,
                    'routingNumber' => $request->routingNumber4
                ],
            ]
        );
        if ($result->success) {
            $account->accountNumber4 = substr($request->accountNumber4, -4);
            $account->routingNumber4 = substr($request->routingNumber4, -4);
            $account->save();
            return view('accounts.edit', compact('account'));
        } else {
            return view('accounts.edit', compact('account'))
                ->withErrors($result->errors->deepAll());
        }
    }

    public function postStatus(Request $request)
    {
        if (isset($request->bt_signature) && isset($request->bt_payload)) {
            $signature = $request->bt_signature;
            $payload = $request->bt_payload;
            $notification = \Braintree_WebhookNotification::parse(
                $signature, $payload
            );
            if ($notification->kind == \Braintree_WebhookNotification::SUB_MERCHANT_ACCOUNT_DECLINED) {
                $id = $notification->merchantAccount->id;
                $account = Profile::findOrFail($id)->account;
                $account->status = $notification->message;
                $account->save();
            } elseif ($notification->kind == \Braintree_WebhookNotification::SUB_MERCHANT_ACCOUNT_APPROVED) {
                $id = $notification->merchantAccount->id;
                $account = Profile::findOrFail($id)->account;
                $account->status = $notification->merchantAccount->status;
                $account->save();
            }
        }
    }

    public function getConnections() {
        return view('accounts.connections');
    }
}
