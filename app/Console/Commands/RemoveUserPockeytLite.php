<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Profile;
use App\Post;
use App\Crypt;
use App\Location;
use Carbon\Carbon;
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
    $timeLimit =  Carbon::now()->subMinutes(20);

    $userLocations = Location::whereNotBetween('updated_at', [$timeLimit, $timeNow])->get();

    if (count($userLocations) > 0 ) {
	    foreach ($userLocations as $userLocation) {
	      $business = Profile::findOrFail($userLocation->location_id);
	      if ($business->account->pockeyt_lite_enabled) {
	        $squareLocationId = $business->account->square_location_id;
	        $itemId = $business->account->square_item_id;
	        $variationId = 'pockeyt' . $userLocation->user_id;
	        $squareToken = $business->square_token;

	        try {
	          $token = Crypt::decrypt($squareToken);
	        } catch (DecryptException $e) {
	          dd($e);
	        }

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
	            return dd($e->getResponse());
	          }
	        }
	        $userLocation->delete();
	      }
	    }
	  }
  }
}
