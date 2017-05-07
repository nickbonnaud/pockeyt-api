<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use JWTAuth;
use Carbon\Carbon;
use DateTimeZone;
use App\Profile;
use App\Transaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SalesController extends Controller
{
	public function __construct() {
		$this->middleware('auth');
    parent::__construct();
 	}

	
  public function show() {
    $currentDate = Carbon::now();
    $fromDate = Carbon::now();
    $fromDate->hour = 4;
    if ($currentDate <= $fromDate) {
    	$fromDate = Carbon::now()->subDay()->hour = 4;
    }
    $profile = $this->user->profile;

    $salesToday = Transaction::where(function($query) use ($fromDate, $currentDate, $profile) {
      $query->whereBetween('updated_at', [$fromDate, $currentDate])
        ->where('profile_id', '=', $profile->id);
    })->get();

    if (!$salesToday) {
    	$salesToday = 0;
    }
    return view('sales.show', compact('salesToday'));
  }

  public function customDate(Request $request) {
  	$fromDate = $request->fromDate;
  	$toDate = $request->toDate;
  	$profileId = $request->businessId;

  	$sales = Transaction::where(function($query) use ($fromDate, $toDate, $profileId) {
      $query->whereBetween('updated_at', [$fromDate, $toDate])
        ->where('profile_id', '=', $profileId);
    })->get();

    if (!$sales) {
    	$sales = 0;
    }
  	return response()->json($sales);
  }
}






