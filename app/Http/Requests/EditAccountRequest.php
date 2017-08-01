<?php

namespace App\Http\Requests;

class EditAccountRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
    	dd($this->route()->parameter('accounts'));
    	$account = $this->route()->parameter('accounts');
    	return \Auth::user()->profile->id == $account->profile_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [];
    }
}
