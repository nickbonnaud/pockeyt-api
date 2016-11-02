<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Socialite;

class ConnectController extends Controller
{
	public function connectFB(Request $request)
	{
		$this->isLoggedInFB($request->has('code'));
	}

	// public function handleProviderCallbackFb()
	// {
	// 	try {
	// 		$userFB = Socialite::driver('facebook')->user();
	// 		dd($userFB);
	// 	} catch (Exception $e) {
	// 		dd($e);
	// 		return redirect('connect/facebook');
	// 	}
	// }

	private function isLoggedInFB($hasCode) {
		if (! $hasCode) return $this->getAuthorization();

		$userFB = Socialite::driver('facebook')->user();
	}

	private function getAuthorization() {
		dd('inside auth');
		return Socialite::driver('facebook')->redirect();
	}
}





