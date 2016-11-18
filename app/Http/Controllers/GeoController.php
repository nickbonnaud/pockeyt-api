<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Location;
use App\Profile;
use App\Http\Requests;
use App\Events\CustomerEnterRadius;
use App\Events\CustomerLeaveRadius;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class GeoController extends Controller
{


    public function putLocation(Request $request)
    {
        $user = User::findOrFail($request->userId);
    	$user['lat'] = $request->lat;
    	$user['lng'] = $request->lng;
    	$user['accuracy'] = $request->accuracy;
    	$user['timestamp'] = $request->timestamp;
        $user['prevLocations'] = $request->lastLocation;
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
                    if (is_null($prevLocations)) {
                        $dbUser->locations()->create([
                            'location_id' => $business->id
                        ]);
                    }
                }
    		} 
    	}
        if (!is_null($user->prevLocations)) {
            foreach ($user->prevLocations as $prevLocation) {
                if ($inLocations == []) {
                    $location = Location::where([
                        ['user_id', '=', $dbUser->id],
                        ['location_id', '=', $prevLocation]
                    ])->get();
                    $location->delete();
                    event(new CustomerLeaveRadius($user, $prevLocation));
                } elseif (!in_array($prevLocation, $inLocations)) {
                    $location = Location::where([
                        ['user_id', '=', $dbUser->id],
                        ['location_id', '=', $prevLocation]
                    ])->get();
                    $location->delete();
                    event(new CustomerLeaveRadius($user, $prevLocation));
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
}
