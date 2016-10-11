<?php

namespace App\Http\Requests;

use App\User;
use App\Http\Requests\Request;

class UpdatePasswordRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
    	$userRoute = $this->route('users');
        if(!is_null($user = \Auth::user())) {
            return $user->is_admin || $user->id == $userRoute;
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
            
        ];
    }
}
