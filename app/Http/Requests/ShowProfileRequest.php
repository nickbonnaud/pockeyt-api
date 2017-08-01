<?php

namespace App\Http\Requests;

class ShowProfileRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
    	$profileId = $this->route()->parameter('profiles');
    	$user = \Auth::user();
    	return $user->profile->id == $profileId;
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
