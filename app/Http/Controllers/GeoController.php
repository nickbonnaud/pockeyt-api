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
        $this->middleware('jwt.auth', ['only' => ['postLocationMonitor']]);
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

    public function postLocationMonitor(Request $request)
    {
        $userT = JWTAuth::parseToken()->authenticate();
        $data = $request->all();
        $data = json_decode(json_encode($data));
        $isHeartBeat = $data->location->is_heartbeat;

        $business = 113;
        $user = $data;
        $this->customerExit($user, $business);
        $user = $userT;

        if (!$isHeartBeat) {
            $geoFence = $data->location->geofence;
            $profile = Profile::findOrFail($geoFence->extras->profile);
            if ($geoFence->action === 'ENTER') {
                $business = $profile->id;
                $this->customerEnter($user, $business);
                return response('ok');
            } elseif ($geoFence->action === 'EXIT') {
                $business = $profile->id;
                $this->customerExit($user, $business);
                return response('ok');
            }
        } elseif ($isHeartBeat) {
            $geoLocation = $data->location->coords;
            $this->checkDistance($user, $geoLocation);
            return response('ok');
        }
    }

    public function checkDistance($user, $geoLocation) {
        $businessCoords = DB::table('geo_locations')->get();
    	$userLat = $geoLocation->latitude;
    	$userLng = $geoLocation->longitude;
        $inLocations = [];
    	foreach ($businessCoords as $businessCoord) {
    		$businessLat = $businessCoord->latitude;
    		$businessLng = $businessCoord->longitude;
			$distance = $this->getDistanceFromLatLng($businessLat, $businessLng, $userLat, $userLng);
			if ($distance <= 100) {
                if (!in_array($businessCoord->profile_id, $inLocations)) {
                    array_push($inLocations, $businessCoord->profile_id);
                    $business = $businessCoord->profile_id;
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
                $business = $storedLocation->profile_id;
                event(new CustomerLeaveRadius($user, $business));
                $storedLocation->delete();
            }
        }
        return;
    }

    public function checkIfUserInLocation($user, $inLocations) {
        $storedLocations = Location::where('user_id', '=', $user->id)->get();
        if (!isset($storedLocations)) { 
            foreach ($inLocations as $inLocation) {
                $this->setLocation($user, $inLocation);
            }
        } else {
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
        }
        return;
    }

    public function setLocation($user, $business) {
        $location = Location::where(function($query) use ($user, $business) {
            $query->where('user_id', '=', $user->id)
                ->where('location_id', '=', $business);
        })->first();
        if (!isset($location)) { 
            return $setLocation = $user->locations()->create([
                'location_id' => $business
            ]);
        }
        return;
    }

    public function removeSetLocation($user, $business) {
        $location = Location::where(function($query) use ($user, $business) {
            $query->where('user_id', '=', $user->id)
                ->where('location_id', '=', $business);
        })->first();
        if (isset($location)) {
           return $location->delete();
        }
        return;
    }

    public function customerEnter($user, $business) {
        event(new CustomerEnterRadius($user, $business));
        return $this->setLocation($user, $business);
    }

    public function customerExit($user, $business) {
        event(new CustomerLeaveRadius($user, $business));
        return $this->removeSetLocation($user, $business);
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






