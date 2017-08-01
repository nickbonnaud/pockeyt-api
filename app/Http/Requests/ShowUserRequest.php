<?php

namespace App\Http\Requests;

class ShowUserRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
    	$user = \Auth::user();
    	return $user->id == $this->route()->parameter('users');
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
