<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Socialite;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
	}

	private function getAuthorization() {
		return Socialite::driver('facebook')
			->fields(['accounts'])->scopes(['pages_show_list'])->redirect();
	}

	private function getAccountsData($userData) {
		$userManagedAccounts = array_get($userData->user, 'accounts.data');

		if (count($userManagedAccounts === 1)) {
			$pageID = array_get($userManagedAccounts, '0.id');
			$access_token = array_get($userManagedAccounts, '0.access_token');
			$this->installApp($pageID, $access_token);
		} 
	}

	private function installApp($pageID, $access_token) {
		$client = new \GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com/v2.8']);

		try {
			$response = $client->request('GET', '$pageID/subscribed_apps', [
        'query' => ['access_token' => $access_token ]
      ]);
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
        return $e->getResponse();
      }
		}

		$data = json_decode($response->getBody());
		dd($data);
	}
}





