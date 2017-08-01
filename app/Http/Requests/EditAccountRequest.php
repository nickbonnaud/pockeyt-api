<?php

namespace App\Http\Requests;

class EditAccountRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
    	$account = $this->route()->parameter('accounts');
    	$user = \Auth::user();

    	return $user->profile()->id == $account->profile_id;
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
