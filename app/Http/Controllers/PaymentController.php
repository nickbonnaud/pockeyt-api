<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use App\User;
use Payline;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    
    public function paylineForm(Request $request) {
        if ($request->has('token')) {
            $authUser = JWTAuth::parseToken()->authenticate();
            if ($authUser) {
                return view('app.payline', compact('authUser'));
            } else {

            }
        } else {
            
        }
    }

    public function initPayline() {
        Payline\Settings::configure([
            "root_url" => 'https://api-test.payline.io',
            "username" => 'US7gYxecvqqmmk3Qyu94YAwk',
            "password" => '2ddb1f42-dc9c-413d-a465-4c7b66ff1ef3']
        );
        return Payline\Bootstrap::init();
    }

    public function createCustomer(Request $request) {
        $tokenizedResponse = $request->tokenizedResponse;
        $user = User::findOrFail($request->userId);

        $this->initPayline();
        $identity = $this->createCustomerID($user);
        $updatedUser = $this->associateToken($tokenizedResponse, $identity, $user);
        return response()->json($updatedUser); 
    }

    public function createCustomerID($user) {
        if ($user->email) {
            $identity = new Payline\Resources\Identity(
                array(
                    "entity" => array(
                        "first_name" => $user->first_name,
                        "last_name" => $user->last_name,
                        "email" => $user->email
                    )
                )
            );
        } else {
            $identity = new Payline\Resources\Identity(
                array(
                    "entity" => array(
                        "first_name" => $user->first_name,
                        "last_name" => $user->last_name
                    )
                )
            );
        }
        return $identity;
    }

    public function associateToken($tokenizedResponse, $identity, $user) {
        dd($tokenizedResponse);
        $card = new Payline\Resources\PaymentInstrument(
            array (
                "token"=> $tokenizedResponse->id,
                "type"=> "TOKEN",
                "identity"=> $identity->id
            )
        );
        $user->customer_id = $card->id;
        $user->card_type = $card->brand;
        $user->last_four_card = $card->last_four;
        $user = $user->save();
        return $user;
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
