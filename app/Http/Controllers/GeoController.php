<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\PushId;
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
        $user = JWTAuth::parseToken()->authenticate();
        $data = $request->all();
        $data = json_decode(json_encode($data));
        $isHeartBeat = $data->is_heartbeat;

        if (!$isHeartBeat && isset($data->geofence)) {
            $geoFence = $data->geofence;
            $profile = Profile::findOrFail($geoFence->extras->profile);
            if ($geoFence->action === 'ENTER') {
                $business = $profile->id;
                $this->customerEnter($user, $business);
                return
            } elseif ($geoFence->action === 'EXIT') {
                $business = $profile->id;
                $this->customerExit($user, $business);
                return response('exit');
            }
        } elseif ($isHeartBeat || (!$isHeartBeat && !isset($data->geofence))) {
            $geoLocation = $data->coords;
            return $this->checkDistance($user, $geoLocation);
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
            if (!isset($storedLocations)) { return response()->json('none');}
            foreach ($storedLocations as $storedLocation) {
                $business = $storedLocation->profile_id;
                event(new CustomerLeaveRadius($user, $business));
                $storedLocation->delete();
            }
            return response()->json('none');
        }
    }

    public function checkIfUserInLocation($user, $inLocations) {
        $storedLocations = Location::where('user_id', '=', $user->id)->get();
        if (!isset($storedLocations)) { 
            foreach ($inLocations as $inLocation) {
                return $this->setLocation($user, $inLocation);
            }
        } else {
            foreach ($storedLocations as $storedLocation) {
                $business = $storedLocation->location_id;
                if (!in_array($business, $inLocations)) {
                    event(new CustomerLeaveRadius($user, $business));
                    $storedLocation->delete();
                } else {
                    $key = array_search($business, $inLocations);
                    unset($inLocations[$key]);
                }
            }
            if (count($inLocations) > 0) {
                foreach ($inLocations as $inLocation) {
                    return $this->setLocation($user, $inLocation);
                }
            }
        }
        return $this->sendResponse($user);
    }

    public function setLocation($user, $business) {
        $location = Location::where(function($query) use ($user, $business) {
            $query->where('user_id', '=', $user->id)
                ->where('location_id', '=', $business);
        })->first();
        if (!isset($location)) {
            $profile = Profile::findOrFail($business);
            $location = $user->locations()->create([
                'location_id' => $business,
                'business_logo' => $profile->logo->thumbnail_url
            ]);
            $bill = Transaction::where(function($query) use ($user,$business) {
                $query->where('user_id', '=', $user->id)
                    ->where('profile_id', '=', $business)
                    ->where('paid', '=', false);
            })->first();
            if (!isset($bill)) {
                $this->sendEnterNotif($user, $business);
            }
            return $location;
        }
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

    public function sendEnterNotif($user, $business) {
        $business = Profile::findOrFail($business);
        $message =  \PushNotification::Message('Pockeyt Pay available for ' . $business->business_name . '. Just say you are paying with Pockeyt!', 
            array(  'category' => 'default',
                    'locKey' => '1',
                    'custom' => array(
                        'inAppMessage' => 'Pockeyt Pay available for ' . $business->business_name . '. Just say you are paying with Pockeyt!'
                    )
        ));
        $token =  PushId::where('user_id', '=', $user->id)->first();
        if ($token->device_type === 'iOS') {
            $pushService = 'PockeytIOS';
        } else {
            $pushService = 'PockeytAndroid';
        }
        return $collection = \PushNotification::app($pushService)
          ->to($token->push_token)
          ->send($message);
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

    public function sendResponse($user) {
        $currentLocations = Location::where('user_id', '=', $user->id)->get();
        return response()->json($currentLocations);
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






