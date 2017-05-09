<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use Carbon\Carbon;
use DateTimeZone;
use App\Profile;
use App\User;
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

    if (($profile->tip_tracking_enabled) && (count($salesToday) != 0)) {
    	$employees = DB::table('users')
        ->leftJoin('transactions', 'users.id', '=', 'employee_id')
        ->whereBetween('transactions.updated_at', [$fromDate, $currentDate])
        ->select('users.id', 'first_name', 'last_name', 'photo_path', 'role', 'employer_id', 'on_shift')
        ->groupBy('users.id')
        ->get();
      $employees = collect($employees);
    }
    return view('sales.show', compact('salesToday', 'employees'));
  }

  public function customDate(Request $request) {
  	$fromDate = $request->fromDate;
  	$toDate = $request->toDate;
  	$profileId = $request->businessId;
  	$profile = Profile::findOrFail($profileId);

  	$sales = Transaction::where(function($query) use ($fromDate, $toDate, $profileId) {
      $query->whereBetween('updated_at', [$fromDate, $toDate])
        ->where('profile_id', '=', $profileId);
    })->get();

    if ($profile->tip_tracking_enabled && (count($sales) != 0)) {
    	$employees = DB::table('users')
        ->leftJoin('transactions', 'users.id', '=', 'employee_id')
        ->whereBetween('transactions.updated_at', [$fromDate, $currentDate])
        ->select('users.id', 'first_name', 'last_name', 'photo_path', 'role', 'employer_id', 'on_shift')
        ->groupBy('users.id')
        ->get();
    }
  	return response()->json(array('sales' => $sales, 'employees' => $employees));
  }

  public function toggleTipTracking() {
  	$profile = $this->user->profile;

  	$profile->tip_tracking_enabled = !$profile->tip_tracking_enabled;
  	$profile->save();
  	return redirect()->back();
  }

}






