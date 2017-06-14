<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    
    public function clientToken() {
        JWTAuth::parseToken()->authenticate();
        $clientToken = \Braintree_ClientToken::generate();
        return response()->json(compact('clientToken'));
    }

    public function createCustomer(Request $request) {
        $authUser = JWTAuth::parseToken()->authenticate();
        $user = User::findOrFail($authUser->id);
        $stripeToken = $request->stripeToken;

        $user->card_type = $request->cardType;
        $user->last_four_card = $request->lastFour;
        $user->save();
        return $this->sendToken($user, $stripeToken);
    }

    public function sendToken($user, $stripeToken) {
        if ($user->email) {
            $customer = Stripe\Customer::create(array(
                'email' => $user->email,
                'source' => $stripeToken
            ));
        } else {
            $customer = Stripe\Customer::create(array(
                'description' => $user->first_name . $user->last_name,
                'source' => $stripeToken
            ));
        }

        $user->customer_id = $customer->id;
        $user->save();
        return response()->json($user);
    }

    public function editPaymentMethod(Request $request) {
        $authUser = JWTAuth::parseToken()->authenticate();
        $result = \Braintree_PaymentMethod::update(
            $request->payToken,
            [
                'paymentMethodNonce' => $request->userNonce
            ]
        );
        if ($result->success) {
            return response('Success', 200);
        } else {
            foreach($result->errors->deepAll() AS $error) {
                return($error->code . ": " . $error->message . "\n");
            }
        }
    }
}
