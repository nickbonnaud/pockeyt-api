<?php

namespace App\Http\Requests;
use Crypt;

class EditAccountRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
    	$accountId = Crypt::decrypt($this->route()->parameter('accounts'));
    	$user = \Auth::user();
    	return $user->profile->account->id == $accountId;
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
