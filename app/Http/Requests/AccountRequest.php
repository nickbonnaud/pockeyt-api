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
            'legalBizName' => 'required',
	        'businessType' => 'required',
	        'bizTaxId' => 'required',
	        'established' => 'required|date_format: Y-m-d',
	        'annualCCSales' => 'required',
	        'bizStreetAdress' => 'required',
	        'bizCity' => 'required',
	        'bizState' => 'required',
	        'bizZip' => 'required',
	        'phone' => 'required',
            'accountEmail' => 'required'
        ];
    }
}
