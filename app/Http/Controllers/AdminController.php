<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use App\Profile;
use App\Account;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
	
	public function __construct() {
    $this->middleware('auth');
    $this->middleware('auth:admin');

    parent::__construct();
 	}

	public function getPendingBusinesses() {
    $businesses = Profile::where('approved', '=', false)->get();
    $accounts = Account::where('status', '=', 'review')->get();

    return view('admin.review', compact('businesses', 'accounts'));
  }
}






