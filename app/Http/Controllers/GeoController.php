<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Profile;
use App\Http\Requests;
use App\Events\CustomerInRadius;
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
    	$business = $this->checkDistance($user);
        return response($business);
    }

    private function checkDistance($user) {
    	$businesses = DB::table('profiles')->select(array('id', 'lat', 'lng'))->get();
    	$userLat = $user->lat;
    	$userLng = $user->lng;
    	foreach ($businesses as $business) {
    		$businessLat = $business->lat;
    		$businessLng = $business->lng;
    		if (($businessLat !== null) && ($businessLng !== null)) {
    			$distance = $this->getDistanceFromLatLng($businessLat, $businessLng, $userLat, $userLng);
    			if ($distance <= 1000) {
                    return $business;
    				// event(new CustomerInRadius($user));
    			}
    		} 
    	}
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
