<?php

namespace App\Http\Requests;

class DeleteAccountRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return !is_null($user = \Auth::user()) && $this->route('accounts') == $user->account->id;
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
