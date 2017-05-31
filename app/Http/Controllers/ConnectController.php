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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ConnectController extends Controller
{
	 public function __construct()
    {
      parent::__construct();
    }

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
		return Socialite::with('facebook')
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
		$profile->connected = true;
		$profile->save();
		flash()->success('Connected!', 'Account connected to Facebook');
    return redirect()->back();
	}

	private function addPageIdToProfileInsta($userData) {
		$profile = $this->user->profile;
		$profile->insta_account_id = $userData->id;
		$profile->insta_account_token = $userData->token;
		$profile->connected = true;
		$profile->save();
		flash()->success('Connected!', 'Account connected to Instagram');
    return redirect()->back();
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
			$access_token = $profile->insta_account_token;
			$clientInsta = new \GuzzleHttp\Client(['base_uri' => 'https://api.instagram.com/v1/media/']);
			try {
				$responseInsta = $clientInsta->request('GET', $mediaId, [
	        'query' => ['access_token' => $access_token ]
	      ]);
			} catch (RequestException $e) {
				if ($e->hasResponse()) {
					dd($e->getResponse());
	        return $e->getResponse();
	      }
			}
			$data = json_decode($responseInsta->getBody());
			return $this->addInstaPost($data, $profile, $mediaId);
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

	public function connectSquare(Request $request) {
    return $this->isLoggedInSquare($request->all());
  }

  public function isLoggedInSquare($data) {
    if ($data['state'] = env('SQUARE_STATE')) return $this->getAccessToken($data['code']);
  }

  public function getAccessToken($code) {
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/oauth2/']);
    try {
      $response = $client->request('POST', 'token', [
        'json' => ['client_id' => env('SQUARE_ID'),
        'client_secret' => env('SQUARE_SECRET'),
        'code'=> $code]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    $body = json_decode($response->getBody());
    $profile = $this->user->profile;
    $profile->square_token = Crypt::encrypt($body->access_token);
    $profile->save();
    flash()->success('Connected!', 'You can now import inventory from Square');
    return redirect()->route('products.list');
  }

  public function removefBSubscription() {
  	$accessToken = $this->user->profile->fb_app_id;
  	$pageId = $this->user->profile->fb_page_id;

		$client = new \GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com/v2.8']);

		try {
			$response = $client->request('DELETE', $pageId . '/subscribed_apps', [
        'query' => ['access_token' => $accessToken ]
      ]);
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
        return $e->getResponse();
      }
		}
		if ($response->success == true) {
			$profile = $this->user->profile;
			$profile->connected = false;
			$profile->save();
			flash()->success('Disabled!', 'Auto updates disabled for Facebook');
    	return redirect()->back();
		} else {
			flash()->overlay('Oops! Unable to disable', 'Please try again', 'error');
    	return redirect()->back();
		}
  }

  public function addfBSubscription() {
  	$access_token = $this->user->profile->fb_app_id;
  	$verifyToken = env('FB_VERIFY_TOKEN');
  	$client = new \GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com/v2.9/']);
		try {
			$response = $client->request('POST',  env('FB_APP_ID') . '/subscriptions', [
				'query' => ['access_token' => $access_token ],
        'object' => 'page',
        'callback_url' => 'https://pockeytbiz.com/connect/subscribe/facebook',
        'fields' => 'feed',
        'verify_token' => $verifyToken
      ]);
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
        return $e->getResponse();
      }
		}
		if ($response->success == true) {
			$profile = $this->user->profile;
			$profile->connected = true;
			$profile->save();
			flash()->success('Enabled!', 'Auto updates enabled for Facebook');
    	return redirect()->back();
		} else {
			flash()->overlay('Oops! Unable to enable', 'Please try again', 'error');
    	return redirect()->back();
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
					$post->fb_post_id = $fbPost['post_id'];

					if (isset($fbPost['message'])) {
						$post->message = $fbPost['message'];
					} else {
						$post->message = "Recent photo from " . $profile->business_name;
					}
				
					if (isset($fbPost['photos'])) {
						$photos = $fbPost['photos'];
						$post->photo_path = $photos[0];
					}
					$post->published_at = Carbon::now(new DateTimeZone(config('app.timezone')));
					$profile->posts()->save($post);
				}
				break;
			case 'photo':
				$existingPost = Post::where('fb_post_id', '=', $fbPost['post_id'])->first();
				if ($existingPost === null) {
					$post = new Post;
					if (isset($fbPost['message'])) {
						$post->message = $fbPost['message'];
					} else {
						$post->message = "Recent photo from " . $profile->business_name;
					}
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

	public function addInstaPost($data, $profile, $mediaId) {
		if ($data->data->type === 'image') {
			$post = new Post;
			if (isset($data->data->caption->text)) {
				$post->message = $data->data->caption->text;
			} else {
				$post->message = "Recent photo from " . $profile->business_name;
			}
			
			$post->insta_post_id = $mediaId;
			$post->photo_path = $data->data->images->standard_resolution->url;
			$post->published_at = Carbon::now(new DateTimeZone(config('app.timezone')));

			$profile->posts()->save($post);
		}
	}
}

	





