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
		return $this->isLoggedInFB($request->has('code'));
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
		$userFB = Socialite::driver('facebook')->fields(['email', 'pages_show_list'])->user();
		dd($userFB);
	}

	private function getAuthorization() {
		$provider = Socialite::driver('facebook');
		$provider->fields(['email', 'pages_show_list'])->scopes(['email', 'pages_show_list'])->redirect();
	}
}





