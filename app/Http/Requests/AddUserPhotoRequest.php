<?php

namespace App\Http\Requests;

use JWTAuth;
use App\User;
use App\Http\Requests\Request;

class AddUserPhotoRequest extends Request {

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
            'file' => 'required|mimes:jpg,jpeg,png,bmp'
        ];
    }
}
