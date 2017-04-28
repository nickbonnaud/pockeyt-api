<?php

namespace App\Http\Controllers;

use App\Post;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AppController extends Controller {
    /**
     * Show home page
     *
     * @return View
     */
    public function index() {
        $signedInUser = $this->user;
        if (isset($signedInUser)) {
            $profile = $signedInUser->profile;
            if (isset($profile)) {
                return view('profiles.show', compact('profile'));
            } else {
                $tags = \App\Tag::lists('name', 'id');
                return view('profiles.create', compact('tags'));
            }
        } else {
            return view('app.index');
        }
    }

    public function privacyPolicy() {
    	return view('app.privacyPolicy');
    }

    public function endPolicy() {
        return view('app.endPolicy');
    }
}
