<?php

namespace App\Http\Requests;

use App\Profile;

class DeleteLoyaltyProgramRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return !is_null($user = \Auth::user()) && ($user->is_admin || $this->route('loyalty_programs') == $user->profile->loyaltyProgram->id);
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
