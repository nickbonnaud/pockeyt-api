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
        return !is_null($user = \Auth::user()) && ($user->profile->loyaltyProgram->contains($this->route('loyalty_programs')));
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
