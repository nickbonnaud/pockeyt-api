<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Socialite;
use App\Post;
use Carbon\Carbon;
use DateTimeZone;
use App\Profile;
use App\Events\BusinessFeedUpdate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ConnectController extends Controller
{
	public function connectFB(Request $request){
		return $this->isLoggedInFB($request->has('code'));
	}

	public function connectInsta(Request $request) {
		return $this->isLoggedInInsta($request->has('code'));
	}

	private function isLoggedInFB($hasCode) {
		if (! $hasCode) return $this->getAuthorizationFB();
		$userData = Socialite::driver('facebook')->fields(['accounts'])->user();
		return $this->getAccountsDataFB($userData);
	}

	private function isLoggedInInsta($hasCode) {
		if (! $hasCode) return $this->getAuthorizationInsta();
		$userData = Socialite::driver('instagram')->user();
		return $this->addPageIdToProfileInsta($userData);
	}

	private function getAuthorizationFB() {
		return Socialite::with('instagram')
			->fields(['accounts'])->scopes(['pages_show_list', 'manage_pages'])->redirect();
	}

	private function getAuthorizationInsta() {
		return Socialite::driver('instagram')
			->redirect();
	}

	private function getAccountsDataFB($userData) {
		$userManagedAccounts = array_get($userData->user, 'accounts.data');

		if (count($userManagedAccounts === 1)) {
			$pageID = array_get($userManagedAccounts, '0.id');
			$access_token = array_get($userManagedAccounts, '0.access_token');

			return $this->installApp($pageID, $access_token);
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
		$data = json_decode($response->getBody());
		if ($data->success === true) {
			return $this->addPageIdToProfileFB($pageID, $access_token);
		}
	}

	private function addPageIdToProfileFB($pageID, $access_token) {
		$profile = $this->user->profile;
		$profile->fb_page_id = $pageID;
		$profile->fb_app_id = $access_token;
		$profile->save();
		return view('profiles.show', compact('profile'));
	}

	private function addPageIdToProfileInsta($userData) {
		$profile = $this->user->profile;
		$profile->insta_account_id = $userData->id;
		$profile->insta_account_token = $userData->token;
		$profile->save();
		return view('profiles.show', compact('profile'));
	}

	public function verifySubscribeFB(Request $request) {
		if (($request->hub_mode == 'subscribe') && ($request->hub_verify_token == env('FB_VERIFY_TOKEN'))) {
			return response($request->hub_challenge);
		}
	}

	public function verifySubscribeInsta(Request $request) {
		if (($request->hub_mode == 'subscribe') && ($request->hub_verify_token == env('INSTA_VERIFY_TOKEN'))) {
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

		return $this->checkIfProfilePageId($body);
	}

	public function receiveInstaMedia(Request $request) {
		$body = $request->getContent();
		$posts = json_decode($body);
		foreach ($posts as $post) {
			$accountId = $post->object_id;
			$mediaId = $post->data->media_id;

			return $this->getInstaPost($accountId, $mediaId);
		}
	}

	private function getInstaPost($accountId, $mediaId) {
			$profile = Profile::where('insta_account_id', '=', $accountId)->first();
			$client = new \GuzzleHttp\Client(['base_uri' => 'https://api.instagram.com/v1/media']);

			try {
				$response = $client->request('GET', $mediaId, [
	        'query' => ['access_token' => $profile->insta_account_token ]
	      ]);
			} catch (RequestException $e) {
				if ($e->hasResponse()) {
					dd($e->getResponse());
	        return $e->getResponse();
	      }
			}
			$data = json_decode($response->getBody());
			event(new BusinessFeedUpdate($data));
			return $this->addInstaPost($data, $profile);
	}


	private function checkIfProfilePageId($body) {
		$updates = json_decode($body, true);
		if ($updates['object'] == 'page') {
			foreach ($updates['entry'] as $entry) {
				$fbPageId = $entry['id'];
				$profile = Profile::where('fb_page_id', '=', $fbPageId)->first();

				if ($profile !== null) {
					return $this->processPost($entry, $profile);
				}
			}
		}
	}

	private function processPost($entry, $profile) {
		foreach ($entry['changes'] as $item) {
			if ($item['field'] == 'feed') {
				$fbPost = $item['value'];

				if ($fbPost['item'] == 'status' || $fbPost['item'] == 'photo' || $fbPost['item'] == 'post') {
					switch ($fbPost['verb']) {
						case 'add':
							return $this->addFbPost($fbPost, $profile);
							break;
						case 'edited':
							$this->editFbPost($fbPost, $profile);
							break;
						case 'remove':
						$this->deleteFbPost($fbPost, $profile);
						break;

						default:
							exit();
							break;
					}
				}
			}
		}
	}

	/**************************
 * Post actions
 */

	public function addFbPost($fbPost, $profile) {

		switch ($fbPost['item']) {
			case 'status':
				$existingPost = Post::where('fb_post_id', '=', $fbPost['post_id'])->first();
				if ($existingPost === null) {
					$post = new Post;
					$post->message = $fbPost['message'];
					$post->fb_post_id = $fbPost['post_id'];
					$post->published_at = Carbon::now(new DateTimeZone(config('app.timezone')));

					$profile->posts()->save($post);
				}
				break;
			case 'photo':
				$existingPost = Post::where('fb_post_id', '=', $fbPost['post_id'])->first();
				if ($existingPost === null) {
					$post = new Post;
					$post->message = $fbPost['message'];
					$post->fb_post_id = $fbPost['post_id'];
					$post->photo_path = $fbPost['link'];
					$post->published_at = Carbon::now(new DateTimeZone(config('app.timezone')));

					$profile->posts()->save($post);
				}
				break;
			default:
				exit();
				break;
		}
	}

	public function editFbPost($fbPost, $profile) {
		$existingPost = Post::where('fb_post_id', '=', $fbPost['post_id'])->first();
			if ($existingPost !== null) {
				$existingPost->message = $fbPost['message'];
				$existingPost->published_at = Carbon::now(new DateTimeZone(config('app.timezone')));

				$profile->posts()->save($existingPost);
			}
	}

	public function deleteFbPost($fbPost, $profile) {
		$existingPost = Post::where('fb_post_id', '=', $fbPost['post_id'])->first();
			if ($existingPost !== null) {
				$existingPost->delete();
			}
	}

	public function addInstaPost($data, $profile) {
		event(new BusinessFeedUpdate($data));
	}

}

	





