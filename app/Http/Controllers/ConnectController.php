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

	private function isLoggedInFB($hasCode) {
		if (! $hasCode) return $this->getAuthorization();
		$userData = Socialite::driver('facebook')->fields(['accounts'])->user();
		return $this->getAccountsData($userData);
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

			return $this->installApp($pageID, $access_token);
		} 
	}

	private function installApp($pageID, $access_token) {
		
		$businesses = Profile::whereNotNull('fb_page_id')->whereNotNull('fb_app_id')->get();

        foreach ($businesses as $business) {

            $pageID = $business->fb_page_id;
            $access_token = $business->fb_app_id;

            $client = new \GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com/v2.8']);

            try {
                $currentTime = time();
                $response = $client->request('GET', $pageID . '/events', [
                    'query' => ['since' => $currentTime,'access_token' => $access_token ]
                ]);
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    dd($e->getResponse());
                    return $e->getResponse();
                }
            }
            $data = json_decode($response->getBody());
            $events = $data->data;
            foreach ($events as $event) {
                $existingEvent = Post::where('fb_post_id', '=', $event->id)->first();
                if ($existingEvent === null) {
                    $post = new Post;
                    event(new BusinessFeedUpdate('should fire'));
                    
                    $post->title = $event->name;
                    $post->body = $event->description;
                    $post->fb_post_id = $event->id;
                    $post->published_at = Carbon::now(new DateTimeZone(config('app.timezone')));
                    
                    $date = strtotime($event->start_time);
                    $formattedDate = date('Y-m-d', $date);
                    $post->event_date = $formattedDate;
                    $clientPhoto = new \GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com/v2.8']);
                    try {
                        $responsePhoto = $clientPhoto->request('GET', $event->id . '/picture', [
                            'query' => ['redirect' => '0', 'access_token' => $access_token ]
                        ]);
                    } catch (RequestException $e) {
                        if ($e->hasResponse()) {
                            dd($e->getResponse());
                            return $e->getResponse();
                        }
                    }
                    $dataPhoto = json_decode($responsePhoto->getBody());
                    $post->photo_path = $dataPhoto->data->url;

                    $business->posts()->save($post);
                }
            }
        }



		// $client = new \GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com/v2.8']);

		// try {
		// 	$response = $client->request('POST', $pageID . '/subscribed_apps', [
  //       'query' => ['access_token' => $access_token ]
  //     ]);
		// } catch (RequestException $e) {
		// 	if ($e->hasResponse()) {
		// 		dd($e->getResponse());
  //       return $e->getResponse();
  //     }
		// }
		// $data = json_decode($response->getBody());
		// if ($data->success === true) {
		// 	return $this->addPageIdToProfile($pageID, $access_token);
		// }
	}

	private function addPageIdToProfile($pageID, $access_token) {
		$profile = $this->user->profile;
		$profile->fb_page_id = $pageID;
		$profile->fb_app_id = $access_token;
		$profile->save();
		return view('profiles.show', compact('profile'));
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

		return $this->checkIfProfilePageId($body);
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

}

	





