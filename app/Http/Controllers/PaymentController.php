<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use App\User;
use App\Http\Controllers\Controller;

use App\Events\CustomerEnterRadius;
use SplashPayments;

class PaymentController extends Controller
{
    
    public function cardForm(Request $request) {
        return view('app.cardInput');
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

    public function receiveSplashToken(Request $request) {
        $tokenData = json_decode($request->getContent());
        $user = $tokenData;
        $business = 1;
        event(new CustomerEnterRadius($user, $business));
    }

    public function setAlert() {
        SplashPayments\Utilities\Config::setTestMode(true);
        SplashPayments\Utilities\Config::setApiKey(env('SPLASH_KEY'));
        $object = new SplashPayments\alerts(
            array(
                "forlogin" => 'g15952a377cbdce',
                'name' => 'token webhook'
            )
        );
        try {
            $object->create();
        }
        catch (SplashPayments\Exceptions\Base $e) {

        }
        if ($object->hasErrors()) {
            dd($object->getErrors());
        } else {
            dd($object->getResponse());
        }
    }
}
