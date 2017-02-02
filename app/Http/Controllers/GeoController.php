<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use App\Location;
use App\Profile;
use App\Transaction;
use App\Http\Requests;
use App\Events\CustomerEnterRadius;
use App\Events\CustomerLeaveRadius;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class GeoController extends Controller
{
     public function __construct()
    {
        parent::__construct();
        $this->middleware('jwt.auth', ['only' => ['postLocation']]);
    }

    public function postLocation(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $data = $request->all();
        $data[0];
    	$user['lat'] = $data[0]->latitide;
    	$user['lng'] = $data[0]->longitude;
    	$user['accuracy'] = $data[0]->accuracy;
    	$user['timestamp'] = $data[0]->timestamp;
        $user['prevLocations'] = $data[0]->lastLocation;
    	$locations = $this->checkDistance($user);
        return response()->json(compact('locations'));
    }

    public function checkDistance($user) {
    	$dbUser = User::findOrFail($user->id);
        $businesses = DB::table('profiles')->select(array('id', 'lat', 'lng'))->get(); 
    	$userLat = $user->lat;
    	$userLng = $user->lng;
        $inLocations = [];
    	foreach ($businesses as $business) {
    		$businessLat = $business->lat;
    		$businessLng = $business->lng;
    		if (($businessLat !== null) && ($businessLng !== null)) {
    			$distance = $this->getDistanceFromLatLng($businessLat, $businessLng, $userLat, $userLng);
    			if ($distance <= 1000) {
                    $inLocations[] = $business->id;
                    $prevLocations = $user->prevLocations;
                    event(new CustomerEnterRadius($user, $business));
                    $savedLocation = $this->checkIfUserInLocation($user, $business);
                    if ((!isset($prevLocations) || empty($prevLocations)) && is_null($savedLocation)) {
                        $this->setLocation($dbUser, $business);
                        return;
                    }
                }
    		} 
    	}
        if (isset($user->prevLocations)) {
            foreach ($user->prevLocations as $prevLocation) {
                if ($inLocations == []) {
                    event(new CustomerLeaveRadius($user, $prevLocation));
                    $location = $this->checkSavedLocation($user, $prevLocation);
                    $location->delete();
                    return;
                } elseif (!in_array($prevLocation, $inLocations)) {
                    event(new CustomerLeaveRadius($user, $prevLocation));
                    $location = $this->checkSavedLocation($user, $prevLocation);
                    $location->delete();
                    return;
                }
            }
        }
        return $inLocations;
    }

    private function getDistanceFromLatLng($businessLat, $businessLng, $userLat, $userLng) {
    	$r = 6371000; // Radius of the earth in m
	    $dLat = $this->deg2rad($userLat-$businessLat);  // deg2rad below
	    $dLon = $this->deg2rad($userLng-$businessLng); 
	    $a = 
	      sin($dLat/2) * sin($dLat/2) +
	      cos(deg2rad($businessLat)) * cos(deg2rad($userLat)) * 
	      sin($dLon/2) * sin($dLon/2)
	      ; 
	    $c = 2 * atan2(sqrt($a), sqrt(1-$a)); 
	    $d = $r * $c; // Distance in m
	    return $d;
    }

    private function deg2rad($deg) {
    	return $deg * (M_PI/180);
   }

    public function checkIfUserInLocation($user, $business) {
        $locationCheck = Location::where(function ($query) use ($user, $business) {
            $query->where('user_id', '=', $user->id)
                ->where('location_id', '=', $business->id);
        })->first();
        return $locationCheck;
    }

    public function checkSavedLocation($user, $prevLocation) {
        $locationCheck = Location::where(function ($query) use ($user, $prevLocation) {
            $query->where('user_id', '=', $user->id)
                ->where('location_id', '=', $prevLocation);
        })->first();
        return $locationCheck;
    }

    public function setLocation($dbUser, $business) {
         return $setLocation = $dbUser->locations()->create([
            'location_id' => $business->id
        ]);
    }

    public function deleteInactiveUser(Request $request) {
        $userId = $request->input('customerId');
        $businessId = $request->input('businessId');
        $locationCheck = Location::where(function ($query) use ($userId, $businessId) {
            $query->where('user_id', '=', $userId)
                ->where('location_id', '=', $businessId);
        })->first();
        $locationCheck->delete();
    }
}






