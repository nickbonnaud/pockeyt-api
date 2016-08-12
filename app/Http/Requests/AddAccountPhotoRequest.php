<?php

namespace App\Http\Requests;

use App\Account;
use App\Http\Requests\Request;

class AddAccountPhotoRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $account = $this->route('accounts');
        if(!is_null($user = \Auth::user())) {
            return $user->is_admin || (!is_null($user->account) && $user->account->id == $account);
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
            'type' => 'required|in:user_photo',
            'photo' => 'required|mimes:jpg,jpeg,png,bmp'
        ];
    }
}
