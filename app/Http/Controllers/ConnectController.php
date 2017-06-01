<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Socialite;
use Crypt;
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
		$profile->connected = 'facebook';
		$profile->save();
		flash()->success('Connected!', 'Account connected to Facebook');
    return redirect()->back();
	}

	private function addPageIdToProfileInsta($userData) {
		$profile = $this->user->profile;
		$profile->insta_account_id = $userData->id;
		$profile->insta_account_token = $userData->token;
		$profile->connected = 'instagram';
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

			$profile = Profile::where('insta_account_id', '=', $accountId)->first();
			$access_token = $profile->insta_account_token;
			if ($access_token) {
				return $this->getInstaPost($mediaId, $profile, $access_token);
			}
		}
	}

	private function getInstaPost($mediaId, $profile, $access_token) {
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
    return redirect()->route('accounts.connections');
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
		$body = json_decode($response->getBody());
		if ($body->success == true) {
			$profile = $this->user->profile;
			$profile->connected = null;
			$profile->save();
			flash()->success('Disabled!', 'Auto updates disabled for Facebook');
    	return redirect()->back();
		} else {
			flash()->overlay('Oops! Unable to disable', 'Please try again', 'error');
    	return redirect()->back();
		}
  }

  public function removeInstaSubscription() {
  	$profile = $this->user->profile;
  	$profile->insta_account_token = null;
  	$profile->connected = null;
  	$profile->save();
  	flash()->success('Disabled!', 'Auto updates disabled for Facebook');
    return redirect()->back();
  }

  public function addfBSubscription() {
  	$accessToken = $this->user->profile->fb_app_id;
  	$pageId = $this->user->profile->fb_page_id;

		$client = new \GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com/v2.8']);

		try {
			$response = $client->request('POST', $pageId . '/subscribed_apps', [
        'query' => ['access_token' => $accessToken ]
      ]);
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
        return $e->getResponse();
      }
		}
		$body = json_decode($response->getBody());
		if ($body->success == true) {
			$profile = $this->user->profile;
			$profile->connected = 'facebook';
			$profile->save();
			flash()->success('Enabled!', 'Auto updates enabled for Facebook');
    	return redirect()->route('accounts.connections');
		} else {
			flash()->overlay('Oops! Unable to enable', 'Please try again', 'error');
    	return redirect()->route('accounts.connections');
		}
  }

  public function subscribeSquare() {
    $squareToken = $this->user->profile->square_token;
    try {
      $token = Crypt::decrypt($squareToken);
    } catch (DecryptException $e) {
      dd($e);
    }
    $squareLocationId = $this->user->profile->account->square_location_id;
    if (!isset($squareLocationId)) {
    	$this->setLocation($token);
    }
    $this->checkSquarePockeytCategory($squareLocationId, $token);
    $this->checkSquareItem($squareLocationId, $token);
    $this->getSquarePages($squareLocationId, $token);
    $this->subscribeEventType($squareLocationId, $token);
    flash()->success('Success', 'Pockeyt Lite connected with Square!');
    return redirect()->route('accounts.connections');
  }

  public function subscribeEventType($squareLocationId, $token) {
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com']);

    try {
      $response = $client->request('PUT', 'v1/' . $squareLocationId . '/webhooks', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ],
        'PAYMENT_UPDATED'
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }       
    $account = $this->user->profile->account;
    $account->pockeyt_lite_enabled = true;
    return $account->save();
  }

  public function setLocation($token) {
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    try {
      $response = $client->request('GET', 'me/locations', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    $body = json_decode($response->getBody());
    if (count($body) > 1) {
      return $this->matchLocationLite($body, $token);
    } elseif(count($body) == 1) {
      $account = $this->user->profile->account;
      $squareLocationId = $body[0]->id;
      $account->square_location_id = $squareLocationId;
      return $account->save();
    }
  }

  public function getSquareLocationId() {
    try {
      $token = Crypt::decrypt($this->user->profile->square_token);
    } catch (DecryptException $e) {
      dd($e);
    }

    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    try {
      $response = $client->request('GET', 'me/locations', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    $body = json_decode($response->getBody());

    if (count($body) > 1) {
      return $this->matchLocation($body);
    } elseif(count($body) == 1) {
      $account = $this->user->profile->account;
      $account->square_location_id = $body[0]->id;
      $account->save();
      flash()->success('Success', 'You can now import inventory from Square');
    	return redirect()->route('accounts.connections');
    }
  }

  public function matchLocation($locations) {
    $businessLocation = $this->user->profile->account->bizStreetAdress;
    if(isset($businessLocation)) {
      foreach ($locations as $location) {
        if ($location->business_address->address_line_1 == $businessLocation) {
          $account = $this->user->profile->account;
          $account->square_location_id = $location->id;
          $account->save();
          flash()->success('Success', 'You can now import inventory from Square');
    			return redirect()->route('accounts.connections');
        } 
      }
      flash()->overlay('Oops', "Your business street address in Pockeyt, " . $businessLocation . ", does not match your saved street address in Square. Please change your address in Pockeyt or Square to match in order to continue.", 'error');
      return redirect()->route('accounts.connections');
    } else {
      flash()->overlay('Oops! Please finish your account', 'Set your business address in the Payment Account Info tab in the Business Info section.', 'error');
      return redirect()->route('accounts.connections');
    }
  }

   public function matchLocationLite($locations, $token) {
    $businessLocation = $this->user->profile->account->bizStreetAdress;
    if(isset($businessLocation)) {
      foreach ($locations as $location) {
        if ($location->business_address->address_line_1 == $businessLocation) {
          $account = $this->user->profile->account;
          $squareLocationId = $location->id;
          $account->square_location_id = $squareLocationId;
         	$account->save();
        }
      }
      if ($this->user->profile->account->square_location_id) {
      	return;
      } else {
      	flash()->overlay('Oops', "Your business street address in Pockeyt, " . $businessLocation . ", does not match your saved street address in Square. Please change your address in Pockeyt or Square to match in order to continue.", 'error');
      	return redirect()->route('accounts.connections');
      }
    } else {
      flash()->overlay('Oops! Please finish your account', 'Set your business address in the Payment Account Info tab in the Business Info section.', 'error');
      return redirect()->route('accounts.connections');
    }
  }

  public function checkSquarePockeytCategory($squareLocationId, $token) {
  	$client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    try {
      $response = $client->request('GET', $squareLocationId . '/categories', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    $categories = json_decode($response->getBody());
    $savedCategoryId = $this->user->profile->account->square_category_id;
    foreach ($categories as $category) {
    	if ($category->name == "Pockeyt Customers" || (isset($savedCategoryId) && $savedCategoryId == $category->id)) {
    		$account = $this->user->profile->account;
	    	$account->square_category_id = $category->id;
	    	return $account->save();
    	}
    }
    return $this->createSquarePockeytCategory($squareLocationId, $token);
  }

  public function createSquarePockeytCategory($squareLocationId, $token) {
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    try {
      $response = $client->request('POST', $squareLocationId . '/categories', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ],
        'json' => ['name' => 'Pockeyt Customers']
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    $category = json_decode($response->getBody());
   	$account = $this->user->profile->account;
	  $account->square_category_id = $category->id;
	  return $account->save();
  }

  public function getSquarePages($squareLocationId, $token) {
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    try {
      $response = $client->request('GET', $squareLocationId . '/pages', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    $pages = json_decode($response->getBody());
    if (count($pages) > 0) {
      foreach ($pages as $page) {
        $pageId = $page->id;
        $row = 4;
        $column = 4;
        return $this->createCell($row, $column, $token, $squareLocationId, $pageId);
      }
    } else {
      return $this->createSquarePage($squareLocationId, $token);
    }
  }

  public function createSquarePage($squareLocationId, $token) {
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    try {
      $response = $client->request('POST', $squareLocationId . '/pages', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ],
        'json' => [
        	'page_index' => 0
        ]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    $page = json_decode($response->getBody());
   	$pageId = $page->id;
    $row = 4;
    $column = 4;
    return $this->createCell($row, $column, $token, $squareLocationId, $pageId);
  }

  public function createCell($row, $column, $token, $squareLocationId, $pageId) {
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    $objectId = $this->user->profile->account->squareCategoryId;
    try {
      $response = $client->request('PUT', $squareLocationId . '/pages' . $pageId . 'cells', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ],
        'json' => [
        	'row' => $row,
	        'column' => $column,
	        'object_type' => 'CATEGORY',
	        'object_id' =>  $objectId
        ]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    return;
  }

  public function checkSquareItem($squareLocationId, $token) {
  	$client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    try {
      $response = $client->request('GET', $squareLocationId . '/items', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    $items = json_decode($response->getBody());
    $savedItemId = $this->user->profile->account->square_item_id;
    foreach ($items as $item) {
    	if ($item->name == "Pockeyt Customer" || (isset($savedItemId) && $savedItemId == $item->id)) {
    		$account = $this->user->profile->account;
	    	$account->square_item_id = $item->id;
	    	return $account->save();
    	}
    }
    return $this->createSquareItem($squareLocationId, $token);
  }

  public function createSquareItem($squareLocationId, $token) {
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    $objectId = $this->user->profile->account->squareCategoryId;
    try {
      $response = $client->request('POST', $squareLocationId . '/items', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ],
        'json' => [
        	'name' => 'Pockeyt Customer',
	        'category_id' => $objectId,
	        'abbreviation' => 'PC',
	        'variations' => [
	          'id' => 'placeholder_variation',
	          'name' => 'Placeholder default Pockeyt Customer',
	          'ordinal' => 100,
	          'pricing_type' => 'FIXED_PRICING',
	          'price_money' => [
	              'amount' => 0,
	              'currency_code' => 'USD'
	          ],
	          'track_inventory' => false,
	        ]
        ]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        dd($e->getResponse());
      }
    }
    $item = json_decode($response->getBody());
    dd($item);
    $account = $this->user->profile->account;
    $account->square_item_id = $item->id;
    return $account->save();
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

	





