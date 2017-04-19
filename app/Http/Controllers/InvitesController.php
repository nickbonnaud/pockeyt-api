<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use App\Invite;
use JWTAuth;
use App\Http\Controllers\Controller;

class InvitesController extends Controller
{
	
	public function __construct() {
    parent::__construct();
    $this->middleware('jwt.auth', ['only' => ['sync']]);
 	}

	public function businessCreate(Request $request) {
		$inviteCode = str_random(6);

		$invite = new Invite;
		$invite->business_id = $request->businessId;
		$invite->invite_code = $inviteCode;
		$this->user->invites()->save($invite);
		return response($inviteCode);
	}
}
