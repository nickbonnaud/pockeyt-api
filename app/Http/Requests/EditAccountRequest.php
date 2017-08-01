<?php

namespace App\Http\Requests;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EditAccountRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        try {
            $accountId = Crypt::decrypt($this->route()->parameter('accounts'));
            $user = \Auth::user();
            return $user->profile->account->id == $accountId;
        } catch(DecryptException $e) {
            return false;
        }
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
