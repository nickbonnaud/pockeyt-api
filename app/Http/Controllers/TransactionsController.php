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
        if (!isset($transaction)) {
            $transaction = null;
        }
        $locationCheck = $this->userInLocationCheck($customer, $business);
        if (isset($locationCheck)) {
            return view('transactions.bill_show', compact('customer', 'business', 'inventory', 'transaction'));
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

    public function update(Request $request) {
        dd("hello");
    }

}







