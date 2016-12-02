<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use App\User;
use App\Product;
use App\Transaction;
use App\Http\Requests;

use App\Http\Controllers\Controller;

class TransactionsController extends Controller
{
    
    public function showBill($customerId) {
        $customer = User::findOrFail($customerId);
        $business = $this->user->profile;
        $inventory = Product::where('profile_id', '=', $business->id)->get();
        $transaction = Transaction::where(function($query) use ($customer, $business) {
            $query->where('user_id', '=', $customer->id)
                ->where('profile_id', '=', $business->id)
                ->where('paid', '=', false);
        })->first();
        $locationCheck = $this->userInLocationCheck($customer, $business);
        if(isset($transaction) && isset($locationCheck)) {
            $bill = $transaction->products;
            $billId = $transaction->id;
            return view('transactions.bill_show', compact('customer', 'business', 'inventory', 'bill', 'billId'));
        } elseif(isset($locationCheck)) {
            return view('transactions.bill_create', compact('customer', 'business', 'inventory'));
        }
    }
    public function userInLocationCheck($customer, $business) {
        $locationCheck = Location::where(function ($query) use ($customer, $business) {
            $query->where('user_id', '=', $customer->id)
                ->where('location_id', '=', $business->id);
        })->first();
        return $locationCheck;
    }

    public function store(Request $request) {
        $transaction = new Transaction($request->all());
        $profile = $this->user->profile;
        $profile->transactions()->save($transaction);

        return view('profiles.show', compact('profile'));
    }

    public function update(Request $request, $id) {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());
        $profile = $this->user->profile;

        return view('profiles.show', compact('profile'));
    }

    public function charge(Request $request) {
        $transaction = new Transaction($request->all());
        $customer = User::findOrFail($transaction->user_id);
        $profile = $this->user->profile;

        $result = $this->createCharge($transaction, $customer, $profile);

        if ($result->success) {
            $transaction->paid = true;
            $profile->transactions()->save($transaction);
            return view('profiles.show', compact('profile'));
        } else {
            $transaction->paid = false;
            $profile->transactions()->save($transaction);
            $bill = $transaction->products;
            $billId = $transaction->id;
            $inventory = Product::where('profile_id', '=', $profile->id)->get();
            
            return view('transactions.bill_show', compact('customer', 'inventory', 'bill', 'billId'))
                ->withErrors($result->errors->deepAll());
        }
    }

    public function chargeExisting(Request $request, $id) {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());
        $customer = User::findOrFail($transaction->user_id);
        $profile = $this->user->profile;

        $result = $this->createCharge($transaction, $customer, $profile);

        if ($result->success) {
            $transaction->paid = true;
            $transaction->save();
            return view('profiles.show', compact('profile'));
        } else {
            $transaction->paid = false;
            $transaction->save();
            $bill = $transaction->products;
            $billId = $transaction->id;
            $inventory = Product::where('profile_id', '=', $profile->id)->get();
            
            return view('transactions.bill_show', compact('customer', 'inventory', 'bill', 'billId'))
                ->withErrors($result->errors->deepAll());
        }
    }

    private function createCharge($transaction, $customer, $profile) {
        $amount = ($transaction->total) / 100;
        $serviceFee = $amount * 0.02;

        $result = \Braintree_Transaction::sale([
            'merchantAccountId' => $profile->id,
            'amount' => $amount,
            'customerId' => $customer->customer_id,
            'customer' => [
                'firstName' => $customer->first_name,
                'lastName' => $customer->last_name,
                'email' => $customer->email,
            ],
            'serviceFeeAmount' => $serviceFee,
            'options' => [
                'submitForSettlement' => True
            ]
        ]);

        return $result;
    }

}







