<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use Carbon\Carbon;
use DateTimeZone;
use App\Profile;
use App\User;
use App\Http\Controllers\Controller;

class EmployeesController extends Controller
{
	public function __construct() {
		$this->middleware('auth');
    parent::__construct();
 	}

	
  public function show() {
    $employees = User::where('employer_id', '=', $this->user->profile->id)->get();

    if (count($employees) != 0) {
    	$employeesOn = [];
    	$employeesOff = [];
	    foreach ($employees as $employee) {
	    	if ($employee->on_shift) {
	    		array_push($employeesOn, $employee);
	    	} else {
	    		array_push($employeesOff, $employee);
	    	}
	    }
    } else {
    	$employeesOn = 0;
    	$employeesOff = 0;
    }
    return view('employees.show', compact('employeesOn', 'employeesOff'));
  }

  public function toggleShift(Request $request) {
  	$businessId = $request->businessId;
  	$employeeId = $request->employeeId;

  	$user = User::where(function($query) use ($businessId, $employeeId) {
    	$query->where('id', '=', $employeeId)
      	->where('employer_id', '=', $businessId);
    })->first();

    $user->on_shift = !$user->on_shift;
    $user->save();
    return response()->json($user);
  }
}






