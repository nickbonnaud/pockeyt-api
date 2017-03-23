<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use JWTAuth;
use Carbon\Carbon;
use DateTimeZone;
use App\Post;
use App\PostAnalytics;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
	
	public function __construct() {
    parent::__construct();
 	}

	public function viewedPosts(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$viewedPosts = $request->all();
		foreach ($viewedPosts as $viewedPost) {
			$post = Post::findOrFail($viewedPost);
			$views = $post->views;
			$post->views = $views + 1;
			$post->save();

			if (isset($user)) {
				$postAnalytics = PostsAnalytics::where(function($query) use ($user, $post) {
          $query->where('user_id', '=', $user->id)
              ->where('post_id', '=', $post->id);
        })->first();

        if (isset($postAnalytics)) {
        	$postAnalytics->viewed = true;
        	$postAnalytics->viewed_on = Carbon::now(new DateTimeZone(config('app.timezone')));
        } else {
        	$postAnalytics = new PostAnalytics;
        	$postAnalytics->business_id = $post->profile_id;
        	$postAnalytics->post_id = $post->id;
        	$postAnalytics->viewed = true;
        	$postAnalytics->viewed_on = Carbon::now(new DateTimeZone(config('app.timezone')));
        }
        $user->postsAnalytics()->save($postAnalytics);
			}
		}
		return response()->json(['success' => 'viewed posts analytics updated'], 200);
	}	
}
