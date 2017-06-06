<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Profile;
use App\Post;
use Crypt;
use App\Location;
use Carbon\Carbon;
use DateTimeZone;
use App\Events\CustomerLeaveRadius;
use GuzzleHttp\Exception\RequestException;

class RemoveUserPockeytLite extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:remove-user-pockeyt-lite';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Remove inactive users from Pockeyt Lite';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $timeNow = Carbon::now();
    $timeLimit = Carbon::now()->subMinutes(5);


    $userLocations = Location::whereNotBetween('updated_at', [$timeLimit, $timeNow])->get();

    if (count($userLocations) > 0 ) {
	    foreach ($userLocations as $userLocation) {
	      $business = Profile::findOrFail($userLocation->location_id);
	      $account = $business->account;
	      if ($account->pockeyt_lite_enabled) {
	        $squareLocationId = $account->square_location_id;
	        $itemId = $account->square_item_id;
	        $variationId = 'pockeyt' . $userLocation->user_id;
	        $squareToken = $business->square_token;
	        $user = $squareToken;
  				$business = 119;
  				event(new CustomerLeaveRadius($user, $business));

	        try {
	          $token = Crypt::decrypt($squareToken);
	        } catch (DecryptException $e) {
	        	$user = $e;
  					$business = 119;
  					event(new CustomerLeaveRadius($user, $business));
	          dd($e);
	        }
	        $user = $token;
  				$business = 119;
  				event(new CustomerLeaveRadius($user, $business));

	        $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);

	        try {
	          $response = $client->request('DELETE', $squareLocationId . '/items' . '/' . $itemId . '/variations' . '/' . $variationId , [
	            'headers' => [
	              'Authorization' => 'Bearer ' . $token,
	              'Accept' => 'application/json'
	            ]
	          ]);
	        } catch (RequestException $e) {
	          if ($e->hasResponse()) {
	          	$user = $e->getResponse();
  						$business = 119;
  						event(new CustomerLeaveRadius($user, $business));
	            return dd($e->getResponse());
	          }
	        }
	        $user = json_decode($response->getBody());
  				$business = 119;
  				event(new CustomerLeaveRadius($user, $business));
	        $userLocation->delete();
	      }
	    }
	  }
  }
}
