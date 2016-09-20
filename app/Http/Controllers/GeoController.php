<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Profile;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class GeoController extends Controller
{

    public function putLocation(Request $request)
    {
    	$user = User::findOrFail($request->userId);
    	$user['lat'] = $request->lat;
    	$user['lng'] = $request->long;
    	$user['accuracy'] = $request->accuracy;
    	$user['timestamp'] = $request->timestamp;
    	checkDistance($user);
    }

    private function checkDistance($user) {
    	$businesses = DB('profiles')->select(array('id', 'lat', 'lng'))->get();
    	$userLat = $user->lat;
    	$userLng = $user->lng;
    	foreach ($businesses as $business) {
    		$businessLat = $business->lat;
    		$businessLng = $business->lng;

    		if (($businessLat !== NULL) && ($businessLng !== NULL)) {
    			$distance = getDistanceFromLatLng($businessLat, $businessLng, $userLat, $userLng);
    			return $distance;
    		}
    	}
    }

    private function getDistanceFromLatLng($businessLat, $businessLng, $userLat, $userLng) {
    	$r = 6371000; // Radius of the earth in m
	    $dLat = deg2rad($userLat-$businessLat);  // deg2rad below
	    $dLon = deg2rad($userLng-$businessLng); 
	    $a = 
	      Math.sin($dLat/2) * Math.sin($dLat/2) +
	      Math.cos(deg2rad($businessLat)) * Math.cos(deg2rad($userLat)) * 
	      Math.sin($dLon/2) * Math.sin($dLon/2)
	      ; 
	    $c = 2 * Math.atan2(Math.sqrt($a), Math.sqrt(1-$a)); 
	    $d = R * $c; // Distance in m
	    return $d;
    }

    private function deg2rad($eg) {
    	return $deg * (Math.PI/180);
   }
}
