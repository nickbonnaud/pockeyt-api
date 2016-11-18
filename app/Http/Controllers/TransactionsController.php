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
        $userId = $user->id;
        $locationCheck = Location::where(function ($query, $userId, $business) {
            $query->where('user_id', '=', $userId)
                ->where('location_id', '=', $business);
        })->first();
        dd($locationCheck);
    }

}