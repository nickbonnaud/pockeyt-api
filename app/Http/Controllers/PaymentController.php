<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use App\User;
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
        $result = \Braintree_Customer::create([
            'firstName' => $authUser->first_name,
            'lastName' => $authUser->last_name,
            'email' => $authUser->email,
            'paymentMethodNonce' => $request->userNonce
        ]);
        if ($result->success) {
            $user = User::findOrFail($authUser->id);
            $user['customer_id'] = $result->customer->id;
            $user->save();
            return response()->json($user);
        } else {
            foreach($result->errors->deepAll() AS $error) {
                return($error->code . ": " . $error->message . "\n");
            }
        }
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
