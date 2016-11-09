<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Profile;
use App\Post;
use GuzzleHttp\Exception\RequestException;

class AddEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new events to business posts';

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
        $businesses = Profile::whereNotNull('fb_page_id')->whereNotNull('fb_app_id')->get();

        foreach ($businesses as $business) {

            $pageID = $business->fb_page_id;
            $access_token = $business->fb_app_id;

            $client = new \GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com/v2.8']);

            try {
                $currentTime = time();
                $response = $client->request('GET', $pageID . '/events?since=' . $currentTime, [
                    'query' => ['access_token' => $access_token ]
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
                $existingEvent = Post::where('fb_post_id', '=', $event->id);
                if ($existingEvent === null) {
                    $post = new Post;
                    $post->title = $event->name;
                    $post->body = $event->description;
                    $post->fb_post_id = $event->id;
                    $post->published_at = Carbon::now(new DateTimeZone(config('app.timezone')));
                    
                    $date = strtotime($event->start_time);
                    $formattedDate = date('Y-m-d', $time);
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
    }
}
