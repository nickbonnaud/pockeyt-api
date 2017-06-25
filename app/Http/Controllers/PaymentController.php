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
        return view('app.payline');
        // if ($request->has('token')) {
        //     $authUser = JWTAuth::parseToken()->authenticate();
        //     if ($authUser) {
        //         return view('app.payline', compact('authUser'));
        //     } else {

        //     }
        // } else {
            
        // }
    }

    public function initPayline() {
        Payline\Settings::configure([
            "root_url" => 'https://api-test.payline.io',
            "username" => 'US7gYxecvqqmmk3Qyu94YAwk',
            "password" => '2ddb1f42-dc9c-413d-a465-4c7b66ff1ef3']
        );
        return Payline\Bootstrap::init();
    }

    public function setPayment(Request $request) {
        $tokenId = $request->tokenId;
        $user = User::findOrFail($request->userId);
        $this->initPayline();
        if (!$user->payline_id) {
            $identity = $this->createPaylineID($user);
            $identityID = $identity->id;
        } else {
            $identityID = $user->payline_id;
        }
        $updatedUser = $this->associateToken($tokenId, $identityID, $user);
        return response()->json($updatedUser); 
    }

    public function createPaylineID($user) {
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
        return $identity = $identity->save();
    }

    public function associateToken($tokenId, $identityID, $user) {
        $card = new Payline\Resources\PaymentInstrument(
            array (
                "token"=> $tokenId,
                "type"=> "TOKEN",
                "identity"=> $identityID
            )
        );
        $card = $card->save();
        $user->customer_id = $card->id;
        $user->payline_id = $identityID;
        $user->card_type = $card->brand;
        $user->last_four_card = $card->last_four;
        $user = $user->save();
        return $user;
    }
}
