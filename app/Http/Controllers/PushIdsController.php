<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use App\PushId;
use App\Http\Requests\PushIdRequest;
use App\Http\Controllers\Controller;

class PushIdsController extends Controller
{
	
	public function __construct() {
    parent::__construct();
    $this->middleware('jwt.auth', ['only' => ['sync']]);
 	}

	public function store(PushIdRequest $request) {
		$dbToken = PushId::where('push_token', '=', $request->push_token)->first();
		if (!isset($dbToken)) {
			$token = new PushId($request->all());
			$token->save();
			return response('ok', 200);
		} else {
			return response('found', 200);
		}
	}

	public function sync(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$token = PushId::where('push_token', '=', $request->push_token)->first();
		if (isset($token)) {
			$token->user_id = $user->id;
			return response('set', 200);
		} else {
			return response('token not found', 200);
		}
	}
}
