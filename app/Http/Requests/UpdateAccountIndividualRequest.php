<?php

namespace App\Http\Requests;

class UpdateAccountIndividualRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        dd($this->route('accounts'));
        return !is_null($user = \Auth::user()) && ($user->is_admin || $this->route('accounts') == $user->profile->account->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
      return [
        'accountUserFirst' => 'required',
        'accountUserLast' => 'required',
        'accountEmail' => 'required',
        'dateOfBirth' => 'required|date_format: Y-m-d',
        'last4' => 'required',
        'indivStreetAdress' => 'required',
        'indivCity' => 'required',
        'indivState' => 'required',
        'indivZip' => 'required'
      ];
    }
}
