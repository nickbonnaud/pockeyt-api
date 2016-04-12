<?php

namespace App\Http\Requests;

use App\Profile;
use App\Http\Requests\Request;

class DeleteProfilePhotoRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $profile = $this->route('profiles');
        if(!is_null($user = \Auth::user())) {
            return $user->is_admin || (!is_null($user->profile) && $user->profile->id == $profile);
        }
        return false;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'type' => 'required|in:logo,hero'
        ];
    }
}
