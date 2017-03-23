<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use JWTAuth;
use App\Post;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
	
	public function __construct() {
    parent::__construct();
 	}

	public function viewedPosts(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$viewedPosts = $request->all();
		if (isset($user)) {
			foreach ($viewedPosts as $viewedPost) {
				$post = Post::findOrFail($viewedPost);
				$views = $post->views;
				$post->views = $views + 1;
				$post->save();
			}
		 	return response($viewedPosts);
		} 
	}	
}
