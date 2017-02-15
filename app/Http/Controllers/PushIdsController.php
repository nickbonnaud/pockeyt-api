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
 	}

	public function store(PushIdRequest $request) {
		$token = new PushId($request->all());
		$token->save();
		return response('ok', 200);
	}
}
