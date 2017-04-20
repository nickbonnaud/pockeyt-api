<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use App\Invite;
use App\User;
use JWTAuth;
use App\Http\Controllers\Controller;

class InvitesController extends Controller
{
	
	public function __construct() {
    parent::__construct();
    $this->middleware('jwt.auth', ['only' => ['userCreate']]);
 	}

	public function businessCreate(Request $request) {
		$inviteCode = str_random(6);

		$invite = new Invite;
		$invite->business_id = $request->businessId;
		$invite->invite_code = $inviteCode;
		$this->user->invites()->save($invite);
		return response($inviteCode);
	}

	public function userCreate(Request $request) {
		$customer = JWTAuth::parseToken()->authenticate();
		if ($customer->id === $request->userId) {
			$user = User::findOrFail($customer->id);
			$inviteCode = str_random(6);

			$invite = new Invite;
			$invite->invite_code = $inviteCode;
			$user->invites()->save($invite);
			return response($inviteCode);
		}
	}
}
