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
  	$user = $this->user;
    $employeesOn = User::where(function($query) use ($user) {
    	$query->where('employer_id', '=', $user->profile->id)
      	->where('on_shift', '=', true);
    })->get();
    $employeesOff = User::where(function($query) use ($user) {
    	$query->where('employer_id', '=', $user->profile->id)
      	->where('on_shift', '=', false);
    })->get();
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






