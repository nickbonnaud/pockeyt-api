<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Socialite;
use App\Post;
use App\Events\BusinessFeedUpdate;
use GuzzleHttp\Client;

use GuzzleHttp\Exception\RequestException;

class ConnectController extends Controller
{
	public function connectFB(Request $request)
	{
		return $this->isLoggedInFB($request->has('code'));
	}

	public function verifySubscribeFB(Request $request) {
		if (($request->hub_mode == 'subscribe') && ($request->hub_verify_token == env('FB_VERIFY_TOKEN'))) {
			return response($request->hub_challenge);
		}
	}

	public function receiveFBFeed(Request $request) {
		$signature = $request->header('x-hub-signature');
		$body = $request->getContent();
		$expected = 'sha1=' . hash_hmac('sha1', $body, env('FB_SECRET'));

		if ($signature != $expected) {
			exit();
		}

		$this->checkIfPostExists($body);
	}

	private function checkIfPostExists($body) {
		$updates = json_decode($body, true);
		if ($updates['object'] == 'page') {
			foreach ($updates['entry'] as $entry) {
				$post = Post::where('fb_post_id', '=', $entry['id'])->first();
				if ($post === null) {
					$this->newPost($entry);
				}
			}
		}
		event(new BusinessFeedUpdate($updates));
	}

	private function newPost($entry) {
		foreach ($entry['changes'] as $post) {
			if ($post['field'] == 'feed') {
				event(new BusinessFeedUpdate($post->value));
			}
		}
	}

	private function isLoggedInFB($hasCode) {
		if (! $hasCode) return $this->getAuthorization();
		$userData = Socialite::driver('facebook')->fields(['accounts'])->user();
		$this->getAccountsData($userData);
	}

	private function getAuthorization() {
		return Socialite::driver('facebook')
			->fields(['accounts'])->scopes(['pages_show_list', 'manage_pages'])->redirect();
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
			$response = $client->request('POST', $pageID . '/subscribed_apps', [
        'query' => ['access_token' => $access_token ]
      ]);
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
        return $e->getResponse();
      }
		}
		$newResponse = $client->request('GET', $pageID . '/subscribed_apps', [
        'query' => ['access_token' => $access_token ]
      ]);
		$data = json_decode($newResponse->getBody());
		dd($data);
	}
}
