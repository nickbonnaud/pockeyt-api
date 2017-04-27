<?php

namespace App\Http\Controllers;

use App\User;
use App\Account;
use App\Profile;
use Illuminate\Http\Request;
use Validator;
use App\Http\Requests;
use App\Http\Requests\AccountRequest;
use App\Http\Requests\UpdateAccountIndividualRequest;
use App\Http\Requests\UpdateAccountBusinessRequest;
use App\Http\Requests\UpdateAccountPayRequest;
use App\Http\Controllers\Controller;

class AccountsController extends Controller
{
    
    public function __construct() {
        $this->middleware('auth', ['except' => ['postStatus', 'testBraintree']]);

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
    public function store(AccountRequest $request)
    {
        if(!is_null($this->user->profile->account))
            return redirect()->route('profiles.show', ['profiles' => $this->user->profile->id]);

        $merchantAccountParams = [
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
            ],
            'business' => [
                'legalName' => $request->legalBizName,
                'taxId' => $request->bizTaxId
            ],
            'funding' => [
                'destination' => \Braintree_MerchantAccount::FUNDING_DESTINATION_BANK,
                'accountNumber' => $request->accountNumber4,
                'routingNumber' => $request->routingNumber4
            ],
            'tosAccepted' => $request->ToS,
            'masterMerchantAccountId' => "pockeytinc",
            'id' => $this->user->profile->id
        ];

        $result = \Braintree_MerchantAccount::create($merchantAccountParams);
        if ($result->success) {
            $data = $request->all();
            $data['status'] = $result->merchantAccount->status;
            $data['accountNumber4'] = substr($request->accountNumber4, -4);
            $data['routingNumber4'] = substr($request->routingNumber4, -4);

            $account = $this->user->profile->publish(
                new Account($data)
            );

            return redirect()->route('profiles.show', ['profiles' => $this->user->profile->id]);
        } else {
            return redirect()->route('accounts.create')
                ->withErrors($result->errors->deepAll());
        }
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
            $notification = \Braintree_WebhookNotification::parse(
                $request->bt_signature, $request->bt_payload
            );
           if ($notification->kind == \Braintree_WebhookNotification::SUB_MERCHANT_ACCOUNT_DECLINED) {
               $account->status = $notification->message;
               $account->save();
           } elseif ($notification->kind == \Braintree_WebhookNotification::SUB_MERCHANT_ACCOUNT_APPROVED) {
                return response('ok');
               $account->status = $notification->merchantAccount->status;
               $account->save();
           } else {
                $account->status = "Uknown Error";
                $account->save();
           }
        }
    }

    public function testBraintree(Request $request) {
        $sampleNotification = \Braintree_WebhookTesting::sampleNotification(
            \Braintree_WebhookNotification::SUB_MERCHANT_ACCOUNT_APPROVED,
            'my_id'
        );

        if (isset($sampleNotification->bt_signature) && isset($sampleNotification->bt_payload)) {
            $notification = \Braintree_WebhookNotification::parse(
                $sampleNotification->bt_signature, $sampleNotification->bt_payload
            );
           if ($notification->kind == \Braintree_WebhookNotification::SUB_MERCHANT_ACCOUNT_DECLINED) {
               dd($notification);
              
           } elseif ($notification->kind == \Braintree_WebhookNotification::SUB_MERCHANT_ACCOUNT_APPROVED) {
                dd($notification);
              
           } else {
                dd($notification);
           }
        } else {
            dd('shit');
        }

    }





}
