<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ChargeRequest extends Request {
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize() {
   return !is_null($user = \Auth::user());
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules() {
    return [
      'user_id' => 'required',
      'products' => 'required',
      'paid' =>'required',
      'total' => 'required'
    ];
  }
}
