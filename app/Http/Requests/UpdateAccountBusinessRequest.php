<?php

namespace App\Http\Requests;

class UpdateAccountBusinessRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return !is_null($user = \Auth::user()) && ($user->is_admin || $this->route('accounts') == $user->profile->account->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
      return [
        'legalBizName' => 'required',
        'bizTaxId' => 'required',
        'bizStreetAdress' => 'required',
        'bizCity' => 'required',
        'bizState' => 'required',
        'bizZip' => 'required'
      ];
    }
}
