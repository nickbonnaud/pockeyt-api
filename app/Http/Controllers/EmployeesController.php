<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use Carbon\Carbon;
use DateTimeZone;
use App\Profile;
use App\User;
use Illuminate\Support\Facades\Hash;
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

  public function search(Request $request) {
  	if ($request->email) {
  		$employee = User::where('email', '=', $request->email)->first();
  	} else {
  		$firstName = $request->firstName;
  		$lastName = $request->lastName;
  		$employee = User::where(function($query) use ($firstName, $lastName) {
	    	$query->where('first_name', '=', $firstName)
	      	->where('last_name', '=', $lastName);
	    })->first();
  	}
  	if ($employee) {
  		return response()->json($employee);
  	} else {
  		return response('User not found');
  	}
  }

  public function employeeAdd(Request $request) {
  	$user = User::findOrFail($request->userId);
  	$user->role = 'employee';
  	$user->employer_id = $request->businessId;
  	$user->save();

  	return response()->json($user);
  }

  public function authorizeRemove(Request $request) {
  	$password = $request->password;
  	$user = User::findOrFail($request->userId);
  	if (Hash::check($password, $user->getAuthPassword())) {
  		return response('unlock');
  	} else {
  		return response('Incorrect Password');
  	}
  }

  public function employeeRemove(Request $request) {
  	$employee = User::findOrFail($request->employeeId);
  	$employee->employer_id = null;
  	$employee->save();
  	return response()->json($employee);
  }

  public function getEmployeesOn(Request $request) {
    $businessId = $request->businessId;
    $employeesOn = User::where(function($query) use ($businessId) {
      $query->where('employer_id', '=', $businessId)
        ->where('on_shift', '=', true);
    })->get();
    return response()->json($employeesOn);
  }
}






