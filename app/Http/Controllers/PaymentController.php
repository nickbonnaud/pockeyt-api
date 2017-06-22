<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use App\User;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    
    public function paylineForm(Request $request) {
        if ($request->has('token')) {
            $authUser = JWTAuth::parseToken()->authenticate();
            if ($authUser) {
                return view('app.payline');
            } else {

            }
        } else {
            dd("none");
        }
    }

    public function createCustomer(Request $request) {
        $response = $request->tokenizedResponse;
        return response()->json($response); 
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
