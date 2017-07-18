<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use App\User;
use App\Http\Controllers\Controller;
use SplashPayments;

class PaymentController extends Controller
{
    
    public function cardForm(Request $request) {
        if ($request->has('token')) {
            $authUser = JWTAuth::parseToken()->authenticate();
            if ($authUser) {
                return view('app.cardInput', compact('authUser'));
            } else {
                return view ('errors.401');
            }
        } else {
            return view ('errors.401');
        }
    }

    public function setPayment(Request $request) {
        $user = User::findOrFail($request->userId);
        $user->last_four_card = $request->number;
        $user->customer_id = $request->token;
        switch ($request->cardType) {
            case 1:
                $user->card_type = 'AMERICAN_EXPRESS';
                break;
            case 2:
                $user->card_type = 'VISA';
                break;
            case 3:
                $user->card_type = 'MASTERCARD';
                break;
            case 4:
                $user->card_type = 'DINERS_CLUB';
                break;
            case 5:
                $user->card_type = 'DISCOVER';
            default:
                $user->card_type = 'GENERIC';
                break;
        }
        $updatedUser = $user->save();
        
        return response()->json($updatedUser); 
    }
}
