<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use App\Location;
use App\Profile;
use App\Transaction;
use App\GeoLocation;
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

    public function getGeoFences() {
        $geoCoords = DB::table('geo_locations')->select('profile_id', 'identifier', 'latitude', 'longitude')->get();
        $geoFences = [];
        foreach ($geoCoords as $geoCoord) {
            $data['latitude'] = $geoCoord->latitude;
            $data['longitude'] = $geoCoord->longitude;
            $data['identifier'] = $geoCoord->identifier;
            $data['radius'] = 100;
            $data['notifyOnEntry'] = true;
            $data['notifyOnExit'] = true;
            $data['extras'] = (object) ['profile' => $geoCoord->profile_id];

            array_push($geoFences, (object) $data);
        }
        return response()->json($geoFences);
    }

    public function postLocationEvent(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $geoData = $request->all();
        $geoFenceEvent = $geoData['action'];

        if (isset($geoFenceEvent)) {
            $business = Profile::findOrFail($geoData['extras']['profile']);
            if ($geoFenceEvent === 'ENTER') {
               return $this->customerEnter($user, $business);
            } elseif ($geoFenceEvent === 'EXIT') {
                return  $this->customerExit($user, $business);
            }
        }
    }

    public function postLocationMonitor(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $geoData = $request->all();
        $geoData = (object) $geoData;

        $business = 113;
        $user = $geoData;

        event(new CustomerEnterRadius($user, $business));
    }

    public function checkDistance($user, $geoLocation) {
        $businesses = DB::table('profiles')->select(array('id', 'lat', 'lng'))->get(); 
    	$userLat = $geoLocation->latitude;
    	$userLng = $geoLocation->longitude;
        $inLocations = [];
    	foreach ($businesses as $business) {
    		$businessLat = $business->lat;
    		$businessLng = $business->lng;
    		if (($businessLat !== null) && ($businessLng !== null)) {
    			$distance = $this->getDistanceFromLatLng($businessLat, $businessLng, $userLat, $userLng);
    			if ($distance <= 1000) {
                    array_push($inLocations, $business->id);
                    event(new CustomerEnterRadius($user, $business));
                }
    		}
    	}
        if (count($inLocations) > 0) {
            return $this->checkIfUserInLocation($user, $inLocations);
        } else {
            $storedLocations = Location::where('user_id', '=', $user->id)->get();
            if (!isset($storedLocations)) { return; }
            foreach ($storedLocations as $storedLocation) {
                event(new CustomerLeaveRadius($user, $storedLocation));
                $storedLocation->delete();
            }
        }
        return;
    }

    public function checkIfUserInLocation($user, $inLocations) {
        $storedLocations = Location::where('user_id', '=', $user->id)->get();
        if (!isset($storedLocations)) { return; }
        foreach ($storedLocations as $storedLocation) {
            if (!in_array($storedLocation->location_id, $inLocations)) {
                event(new CustomerLeaveRadius($user, $storedLocation));
                $storedLocation->delete();
            } else {
                $key = array_search($storedLocation->location_id, $inLocations);
                unset($inLocations[$key]);
            }
        }
        if (count($inLocations) > 0) {
            foreach ($inLocations as $inLocation) {
                $this->setLocation($user, $inLocation);
            }
        }
        return;
    }

    public function setLocation($user, $business) {
        $location = Location::where(function($query) use ($user, $business) {
            $query->where('user_id', '=', $user->id)
                ->where('location_id', '=', $business->id);
        })->first();
        if (!isset($location)) { 
            return $setLocation = $user->locations()->create([
                'location_id' => $business->id
            ]);
        }
        return;
    }

    public function removeSetLocation($user, $business) {
        $location = Location::where(function($query) use ($user, $business) {
            $query->where('user_id', '=', $user->id)
                ->where('location_id', '=', $business->id);
        })->first();
        if (isset($location)) {
           return $location->delete();
        }
        return;
    }

    public function customerEnter($user, $business) {
        event(new CustomerEnterRadius($user, $business));
        $this->setLocation($user, $business);
        return response('ok');
    }

    public function customerExit($user, $business) {
        event(new CustomerLeaveRadius($user, $business));
        $this->removeSetLocation($user, $business);
        return response('ok');
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






