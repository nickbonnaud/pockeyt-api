<?php

namespace App\Http\Requests;

class UpdateBusinessLocationRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return !is_null($user = \Auth::user()) && ($user->is_admin || $this->route('profiles') == $user->profile->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
      return [
        'lat' => 'required',
        'lng' => 'required'
      ];
    }
}
