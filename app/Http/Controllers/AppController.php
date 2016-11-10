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
      return view('app.index');
    }

    public function policy() {
    	return view('app.policy');
    }
}
