<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\PushId;
use JWTAuth;
use Crypt;
use App\Location;
use App\Profile;
use App\Transaction;
use App\GeoLocation;
use App\Http\Requests;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
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
        $geoCoords = GeoLocation::with('profile.logo')->get();
        $geoFences = [];
        foreach ($geoCoords as $geoCoord) {
            if ($geoCoord->profile->account->status === "active") {
                $thumbnail_url = $geoCoord->profile->logo->thumbnail_url;
                $data['latitude'] = $geoCoord->latitude;
                $data['longitude'] = $geoCoord->longitude;
                $data['identifier'] = $geoCoord->identifier;
                $data['radius'] = 50;
                $data['notifyOnEntry'] = true;
                $data['notifyOnExit'] = true;
                $data['extras'] = (object) [
                    'business_logo' => $thumbnail_url,
                    'location_id' => $geoCoord->profile_id
                ];

                array_push($geoFences, (object) $data);
            }
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
                return $this->sendResponse($user);
            } elseif ($geoFence->action === 'EXIT') {
                $business = $profile->id;
                $this->customerExit($user, $business);
                return $this->sendResponse($user);
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
			if ($distance <= 50) {
                if (!in_array($businessCoord->profile_id, $inLocations)) {
                    array_push($inLocations, $businessCoord->profile_id);
                    $business = $businessCoord->profile_id;
                    $status = "enter";
                    $this->pockeytLite($user, $business, $status);
                    event(new CustomerEnterRadius($user, $business));
                }
            }
    	}
        if (count($inLocations) > 0) {
            return $this->checkIfUserInLocation($user, $inLocations);
        } else {
            $storedLocations = Location::where('user_id', '=', $user->id)->get();
            if (!isset($storedLocations)) { return response('none');}
            foreach ($storedLocations as $storedLocation) {
                $business = $storedLocation->location_id;
                $status = "exit";
                $this->pockeytLite($user, $business, $status);
                event(new CustomerLeaveRadius($user, $business));
                $storedLocation->delete();
            }
            return response('none');
        }
    }

    public function checkIfUserInLocation($user, $inLocations) {
        $storedLocations = Location::where('user_id', '=', $user->id)->get();
        if (!isset($storedLocations)) { 
            foreach ($inLocations as $inLocation) {
                $this->setLocation($user, $inLocation);
            }
        } else {
            foreach ($storedLocations as $storedLocation) {
                $business = $storedLocation->location_id;
                if (!in_array($business, $inLocations)) {
                    $status = "exit";
                    $this->pockeytLite($user, $business, $status);
                    event(new CustomerLeaveRadius($user, $business));
                    $storedLocation->delete();
                } else {
                    $storedLocation->touch();
                    $key = array_search($business, $inLocations);
                    unset($inLocations[$key]);
                }
            }
            if (count($inLocations) > 0) {
                foreach ($inLocations as $inLocation) {
                    $this->setLocation($user, $inLocation);
                }
            }
        }
        return $this->sendResponse($user);
    }

    public function setLocation($user, $business) {
        $profile = Profile::findOrFail($business);
        $location = Location::where(function($query) use ($user, $profile) {
            $query->where('user_id', '=', $user->id)
                ->where('location_id', '=', $profile->id);
        })->first();
        if (!$location) {
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
                $this->sendEnterNotif($user, $profile);
            }
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

    public function sendEnterNotif($user, $business) {
        $token =  PushId::where('user_id', '=', $user->id)->first();
        if ($token->device_type === 'iOS') {
            $message =  \PushNotification::Message('Pockeyt Pay available for ' . $business->business_name . '. Just say you are paying with Pockeyt!', 
                array(  'category' => 'default',
                        'locKey' => '1',
                        'custom' => array(
                            'inAppMessage' => 'Pockeyt Pay available for ' . $business->business_name . '. Just say you are paying with Pockeyt!'
                        )
            ));
            $pushService = 'PockeytIOS';
        } else {
            $message =  \PushNotification::Message('Pockeyt Pay available for ' . $business->business_name . '. Just say you are paying with Pockeyt!', 
                array(
                    'category' => 'default',
                    'title' => 'Pockeyt Pay Available',
                    'locKey' => '1',
                    'custom' => array(
                    'inAppMessage' => 'Pockeyt Pay available for ' . $business->business_name . '. Just say you are paying with Pockeyt!'
                        )
            ));
            $pushService = 'PockeytAndroid';
        }
        $collection = \PushNotification::app($pushService)
          ->to($token->push_token)
          ->send($message);
    }

    public function customerEnter($user, $business) {
        $status = "enter";
        $this->pockeytLite($user, $business, $status);
        event(new CustomerEnterRadius($user, $business));
        return $this->setLocation($user, $business);
    }

    public function customerExit($user, $business) {
        $status = "exit";
        $this->pockeytLite($user, $business, $status);
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
        if (!$locationCheck) {
            return;
        } else {
            $status = "exit";
            $user = User::findOrFail($userId);
            $this->pockeytLite($user, $businessId, $status);
            $locationCheck->delete();
        }
    }

    public function getActiveUsers(Request $request) {
        $business = $request->input('businessId');
        $usersInLocation = Location::where('location_id', '=', $business)->get();
        if (isset($usersInLocation)) {
            $users = [];
            foreach ($usersInLocation as $userLocation) {
                $timeIdle = strtotime("now") - strtotime($userLocation->updated_at);
                $user = User::findOrFail($userLocation->user_id);
                if ($timeIdle > 600) {
                    $status = "exit";
                    $this->pockeytLite($user, $business, $status);
                    $userLocation->delete();
                } else {
                    array_push($users, $user);
                }
            }
            return response()->json($users);
        } else {
            return response('none');
        }
    }

    public function sendResponse($user) {
        $currentLocations = DB::table('locations')->select('location_id', 'business_logo')->get();
        return response()->json($currentLocations);
    }

    public function pockeytLite($user, $businessId, $status) {
        $business = Profile::findOrFail($businessId);

        if ($business->account->pockeyt_lite_enabled) {
            $squareLocationId = $business->account->square_location_id;
            $itemId = $business->account->square_item_id;
            $variationId = 'pockeyt' . $user->id;
            $squareToken = $business->square_token;

            try {
              $token = Crypt::decrypt($squareToken);
            } catch (DecryptException $e) {
              dd($e);
            }


            $client = new Client([
                'base_url' => ['https://connect.squareup.com/{version}/', ['version' => 'v1']]
            ]);
            // $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
            if ($status == "enter") {
                try {
                    $response = $client->post($squareLocationId . '/items' . '/' . $itemId . '/variations', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token,
                            'Accept' => 'application/json'
                        ],
                        'json' => [
                            'id' => $variationId,
                            'name' => $user->first_name . ' ' . $user->last_name,
                            'price_money' => [
                                'currency_code' => 'USD',
                                'amount' => 0,
                            ]
                        ]
                    ]);
                } catch(RequestException $e) {
                    if ($e->hasResponse()) {
                        return $e->getResponse();
                    }
                }


                // try {
                //     $response = $client->request('POST', $squareLocationId . '/items' . '/' . $itemId . '/variations', [
                //         'headers' => [
                //             'Authorization' => 'Bearer ' . $token,
                //             'Accept' => 'application/json'
                //         ],
                //         'json' => [
                //             'id' => $variationId,
                //             'name' => $user->first_name . ' ' . $user->last_name,
                //             'price_money' => [
                //                 'currency_code' => 'USD',
                //                 'amount' => 0,
                //             ]
                //         ]
                //     ]);
                // } catch (RequestException $e) {
                //     if ($e->hasResponse()) {
                //         return $e->getResponse();
                //     }
                // }


                return;
            } else {
                try {
                    $response = $client->delete($squareLocationId . '/items' . '/' . $itemId . '/variations' . '/' . $variationId, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token,
                            'Accept' => 'application/json'
                        ]
                    ]);
                } catch(RequestException $e) {
                    if ($e->hasResponse()) {
                        return $e->getResponse();
                    }
                }



                // try {
                //     $response = $client->request('DELETE', $squareLocationId . '/items' . '/' . $itemId . '/variations' . '/' . $variationId , [
                //         'headers' => [
                //             'Authorization' => 'Bearer ' . $token,
                //             'Accept' => 'application/json'
                //         ]
                //     ]);
                // } catch (RequestException $e) {
                //     if ($e->hasResponse()) {
                //         return $e->getResponse();
                //     }
                // }
                return;
            }
        } else {
            return;
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






