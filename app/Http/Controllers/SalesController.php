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

    $employees = [];
    if (($profile->tip_tracking_enabled) && (count($salesToday) != 0)) {
    	$employeeIds = [];
    	foreach ($salesToday as $sale) {
    		if (!in_array($sale->employee_id, $employeeIds)) {
    			array_push($employeeIds, $sale->employee_id);
    			array_push($employees, User::findOrFail($sale->employee_id));
    		}
    	}
    }
    $employees = array('employees' => $employees);
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

    if (count($sales) == 0) {
    	$sales = 0;
    }

    $employees = [];
    if ($profile->tip_tracking_enabled && $sales != 0) {
    	$employeeIds = [];
    	foreach ($sales as $sale) {
    		if (!in_array($sale->employee_id, $employeeIds)) {
    			array_push($employeeIds, $sale->employee_id);
    			array_push($employees, User::findOrFail($sale->employee_id));
    		}
    	}
    }
    if (count($employees == 0)) {
  		$employees = 0;
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






