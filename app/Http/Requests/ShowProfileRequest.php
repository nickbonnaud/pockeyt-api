<?php

namespace App\Http\Requests;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ShowProfileRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
         try {
            $user = \Auth::user();
            return Crypt::decrypt($this->route('profiles')) == $user->profile->id;
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
