<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Socialite;

class ConnectController extends Controller
{
	public function redirectToProviderFb()
	{
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
}
