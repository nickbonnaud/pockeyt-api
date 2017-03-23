<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use JWTAuth;
use App\Http\Controllers\Controller;

class PushIdsController extends Controller
{
	
	public function __construct() {
    parent::__construct();
 	}

	public function viewedPosts(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		if (isset($user)) {
		 	$viewedPosts = $request->all();
		 	return response($viewedPosts);
		} 
	}	

	public function sync(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$token = PushId::where('push_token', '=', $request->push_token)->first();
		if (isset($token)) {
			$token->user_id = $user->id;
			$token->save();
			return response('set', 200);
		} else {
			return response('token not found', 200);
		}
	}
}
