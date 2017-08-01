<?php

namespace App\Http\Requests;

class EditProfileRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        try {
            $profileId = Crypt::decrypt($this->route()->parameter('profiles'));
        } catch(DecryptException $e) {
            return false;
        }
        return !is_null($user = \Auth::user()) && ($user->is_admin || $profileId == $user->profile->id);
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
