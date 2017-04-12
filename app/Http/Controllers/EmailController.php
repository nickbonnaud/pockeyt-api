<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use App\Transaction;
use Mail;
use App\User;
use App\Http\Controllers\Controller;

class EmailController extends Controller
{
	
	public function __construct() {
    parent::__construct();
 	}

	public function show() {
		$profile = $this->user->profile;
		$transaction = Transaction::where('profile_id', '=', $profile->id)->first();
		$customer = User::findOrFail($transaction->user_id);
		$items = $transaction->products;
		$items = json_decode($items);

	return Mail::send('emails.receipt', ['items' => $items, 'profile' => $profile, 'transaction' => $transaction], function($m) use ($customer, $profile) {
          $m->from('receipts@pockeyt.com', 'Pockeyt Receipts');
          $m->to($customer->email, $customer->first_name)->subject('Recent transaction from Pockeyt');
      });
	}
}
