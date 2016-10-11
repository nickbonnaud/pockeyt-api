<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AccountRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
          'accountUserFirst' => 'required',
	        'accountUserLast' => 'required',
	        'accountEmail' => 'required',
	        'dateOfBirth' => 'required|date_format: Y-m-d',
	        'last4' => 'required',
	        'indivStreetAdress' => 'required',
	        'indivCity' => 'required',
	        'indivState' => 'required',
	        'indivZip' => 'required',
	        'routingNumber4' => 'required',
        	'accountNumber4' => 'required'
        ];
    }
}
