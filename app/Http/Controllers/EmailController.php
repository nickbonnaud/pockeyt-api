<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use App\Transaction;
use App\Http\Controllers\Controller;

class EmailController extends Controller
{
	
	public function __construct() {
    parent::__construct();
 	}

	public function show() {
		$profile = $this->user->profile;
		$transaction = Transaction::where('profile_id', '=', $profile->id)->first();
		$items = $transaction->products;
		$items = json_decode($items);

		return view('emails.receipt', compact('transaction', 'items', 'profile'));
	}
}
