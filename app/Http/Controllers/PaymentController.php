<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use App\User;
use App\Http\Controllers\Controller;

use App\Events\CustomerEnterRadius;

class PaymentController extends Controller
{
    
    public function cardForm(Request $request) {
        return view('app.cardInput');
        // if ($request->has('token')) {
        //     $authUser = JWTAuth::parseToken()->authenticate();
        //     if ($authUser) {
        //         return view('app.cardInput', compact('authUser'));
        //     } else {
        //         return view ('errors.401');
        //     }
        // } else {
        //     return view ('errors.401');
        // }
    }

    public function setPayment(Request $request) {
        $user = $request->all();
        $business = 1;
        event(new CustomerEnterRadius($user, $business));


        // $user = User::findOrFail($request->userId);
        
        // if (!$user->payline_id) {
        //     $identity = $this->createPaylineID($user);
        //     $identityID = $identity->id;
        // } else {
        //     $identityID = $user->payline_id;
        // }
        // $updatedUser = $this->associateToken($tokenId, $identityID, $user);
        // return response()->json($updatedUser); 
    }
}
