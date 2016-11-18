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
        $user['prevLocations'] = [113];
        foreach ($user->prevLocations as $prevLocation) {
            $location = $this->checkSavedLocation($user, $prevLocation);
            $location->delete();
        }
    }
    public function checkSavedLocation($user, $prevLocation) {
        $locationCheck = Location::where(function ($query) use ($user, $prevLocation) {
            $query->where('user_id', '=', $user->id)
                ->where('location_id', '=', $prevLocation);
        })->first();
        return $locationCheck;
   }

}