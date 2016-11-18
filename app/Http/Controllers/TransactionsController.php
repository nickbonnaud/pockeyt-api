<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use App\User;
use App\Http\Requests;

use App\Http\Controllers\Controller;

class TransactionsController extends Controller
{
    
    public function createTransaction($customerId) {
        $user = User::findOrFail($customerId);
        $business = 113;
        $locationCheck = $this->checkIfUserInLocation($user, $business);
        dd($locationCheck);
    }
    public function checkIfUserInLocation($user, $business) {
        $locationCheck = Location::where(function ($query) use ($user, $business) {
            $query->where('user_id', '=', $user->id)
                ->where('location_id', '=', $business->id);
        })->first();
        return $locationCheck;
   }

}