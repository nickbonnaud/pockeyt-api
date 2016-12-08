<?php

namespace App\Http\Requests;

class UpdateChargeRequest extends Request {
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize() {
    $isAuthorized = false;
    if (!is_null($user = \Auth::user())) {
      $transactions = \Auth::user()->profile->transactions->where('paid', '=', false);
      foreach ($transactions as $transaction) {
        if ($transaction->id == $this->route('transactionId')) {
          $isAuthorized = true;
        }
      }
    }
    return $isAuthorized;
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
      'paid' => 'required',
      'total' => 'required'
    ];
  }
}
