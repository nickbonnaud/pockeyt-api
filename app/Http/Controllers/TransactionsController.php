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
        $user = User::where('id', '=', $customerId)->get();
        // $locationCheck = Location::where('user_id', '=', $user->id)->first();
        dd($user);
    }

}