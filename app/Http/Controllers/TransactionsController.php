<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use App\User;
use App\Profile;
use App\Http\Requests;

use App\Http\Controllers\Controller;

class TransactionsController extends Controller
{
    
    public function createTransaction($customerId) {
        $customer = User::findOrFail($customerId);
        $business = $this->user->profile;
        $locationCheck = $this->userInLocationCheck($customer, $business);
        if (isset($locationCheck)) {
            return view('transactions.create', compact('customer', 'business'));
        }
    }
    public function userInLocationCheck($customer, $business) {
        $locationCheck = Location::where(function ($query) use ($customer, $business) {
            $query->where('user_id', '=', $customer->id)
                ->where('location_id', '=', $business->id);
        })->first();
        return $locationCheck;
   }

}