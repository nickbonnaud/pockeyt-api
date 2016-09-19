<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class GeoController extends Controller
{

    public function putLocation(Request $request)
    {
    	
    	$user = User::findOrFail($request->userId);
    	$user['lat'] = $request->coords->latitude;
    	$user['long'] = $request->coords->longitude;
    	$user['accuracy'] = $request->coords->accuracy;
    	$user['timestamp'] = $request->timestamp;
    	return $user;
    }
}
