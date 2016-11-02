<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Socialite;

class ConnectController extends Controller
{
	public function redirectToProviderFb(Request $request)
	{
		$this->isLoggedInFB($request->has('code'));
		return Socialite::driver('facebook')->redirect();
	}

	public function handleProviderCallbackFb()
	{
		try {
			$user = Socialite::driver('facebook')->user();
			dd($user);
		} catch (Exception $e) {
			dd($e);
			return redirect('connect/facebook');
		}
	}

	private function isLoggedInFB($hasCode) {
		if (! $hasCode) return $this->getAuthorization();

		$user = Socialite::driver('facebook')->user();
		dd($user);
	}

	private function getAuthorization() {
		return Socialite::driver('facebook')->redirect();
	}
}





