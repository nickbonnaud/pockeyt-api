<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use JWTAuth;
use Carbon\Carbon;
use DateTimeZone;
use App\Post;
use App\Transaction;
use App\PostAnalytic;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
	
	public function __construct() {
    parent::__construct();
 	}

	
  public function show() {
    $currentDate = Carbon::now();
    $fromDate = Carbon::now()->subWeek();
    $profile = $this->user->profile;

    $mostInteracted = Post::where(function($query) use ($fromDate, $currentDate, $profile) {
      $query->whereBetween('published_at', [$fromDate, $currentDate])
        ->where('profile_id', '=', $profile->id);
    })->orderBy('total_interactions', 'desc')->get();

    $mostRevenueGenerated = Post::where(function($query) use ($fromDate, $currentDate, $profile) {
      $query->whereBetween('published_at', [$fromDate, $currentDate])
        ->where('profile_id', '=', $profile->id);
    })->orderBy('total_revenue', 'desc')->get();

    dd($mostInteracted);

    return view('analytics.show', compact('mostInteracted', 'mostRevenueGenerated'));
  }


  public function viewedPosts(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$viewedPosts = $request->all();
		foreach ($viewedPosts as $viewedPost) {
			$post = Post::findOrFail($viewedPost);

			$views = $post->views;
      $total_interactions = $post->total_interactions;

			$post->views = $views + 1;
      $post->total_interactions = $total_interactions + 1;

			$post->save();

			if (isset($user)) {
				$postAnalytic = PostAnalytic::where(function($query) use ($user, $post) {
          $query->where('user_id', '=', $user->id)
              ->where('post_id', '=', $post->id);
        })->first();

        if (isset($postAnalytic)) {
        	$postAnalytic->viewed = true;
        	$postAnalytic->viewed_on = Carbon::now(new DateTimeZone(config('app.timezone')));
        } else {
        	$postAnalytic = new PostAnalytic;
        	$postAnalytic->business_id = $post->profile_id;
        	$postAnalytic->post_id = $post->id;
        	$postAnalytic->viewed = true;
        	$postAnalytic->viewed_on = Carbon::now(new DateTimeZone(config('app.timezone')));
        }
        $user->postAnalytics()->save($postAnalytic);
			}
		}
		return response()->json($viewedPosts);
	}

	public function interactionPosts(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$type = $request->type;
		$post = Post::findOrFail($request->postId);

		if ($type === 'share') {
			$shares = $post->shares;
      $total_interactions = $post->total_interactions;

      $post->total_interactions = $total_interactions + 1;
			$post->shares = $shares + 1;
		} elseif ($type === 'bookmark') {
			$action = $request->action;
			if ($action === 'add') {
				$bookmarks = $post->bookmarks;
        $total_interactions = $post->total_interactions;

        $post->total_interactions = $total_interactions + 1;
				$post->bookmarks = $bookmarks + 1;
			} elseif ($action === 'remove') {
				$bookmarks = $post->bookmarks;
        $total_interactions = $post->total_interactions;

        $post->total_interactions = $total_interactions - 1;
				$post->bookmarks = $bookmarks - 1;
			}
		}
		$post->save();

		if (isset($user)) {
			$postAnalytic = PostAnalytic::where(function($query) use ($user, $post) {
        $query->where('user_id', '=', $user->id)
            ->where('post_id', '=', $post->id);
      })->first();

      if (isset($postAnalytic)) {
      	if ($type === 'share') {
      		$postAnalytic->shared = true;
      		$postAnalytic->shared_on = Carbon::now(new DateTimeZone(config('app.timezone')));
      	} elseif ($type === 'bookmark') {
      		if ($action === 'add') {
      			$postAnalytic->bookmarked = true;
      			$postAnalytic->bookmarked_on = Carbon::now(new DateTimeZone(config('app.timezone')));
      		} elseif ($action === 'remove') {
      			$postAnalytic->bookmarked = false;
      			$postAnalytic->bookmarked_on = null;
      		}
      	}
      } else {
      	$postAnalytic = new PostAnalytic;
      	$postAnalytic->business_id = $post->profile_id;
      	$postAnalytic->post_id = $post->id;
      	if ($type === 'share') {
      		$postAnalytic->shared = true;
      		$postAnalytic->shared_on = Carbon::now(new DateTimeZone(config('app.timezone')));
      	} elseif ($type === 'bookmark') {
      		if ($action === 'add') {
      			$postAnalytic->bookmarked = true;
      			$postAnalytic->bookmarked_on = Carbon::now(new DateTimeZone(config('app.timezone')));
      		} elseif ($action === 'remove') {
      			$postAnalytic->bookmarked = false;
      			$postAnalytic->bookmarked_on = null;
      		}
      	}
      }
      $user->postAnalytics()->save($postAnalytic);
		}
		return response()->json(['success' => 'Updated post analytics'], 200);
	}
}
