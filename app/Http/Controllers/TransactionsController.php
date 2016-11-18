<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;

use App\Http\Controllers\Controller;

class TransactionsController extends Controller
{
    
    public function createTransaction($customerId) {
        $user = User::findOrFail($customerId);
        $business = 113;
        $locationCheck = DB::table('locations')
            ->where(function ($query, $user, $business) {
                $query->where('user_id', '=', $user->id)
                    ->where('location_id', '=', $business);
            })
            ->get();
        dd($locationCheck);
    }

}