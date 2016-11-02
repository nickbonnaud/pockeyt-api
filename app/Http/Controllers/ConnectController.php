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
		$userData = Socialite::driver('facebook')->fields(['accounts'])->user();

		$this->getAccountsData($userData);
		dd(count(array_get($userManagedAccounts->user, 'accounts.data')));
	}

	private function getAuthorization() {
		return Socialite::driver('facebook')
			->fields(['accounts'])->scopes(['pages_show_list'])->redirect();
	}

	private function getAccountsData($userData) {
		$userManagedAccounts = array_get($userData->user, 'accounts.data');

		if (count($userManagedAccounts === 1)) {
			$pageID = array_get($userManagedAccounts, '0.id');
			dd($pageID);
		} 
	}
}





