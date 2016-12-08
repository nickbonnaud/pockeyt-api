<?php

namespace App\Http\Requests;

class UpdateTransactionRequest extends Request {
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
            	dd($this->route());
                if ($transaction->id == $this->route('transactions')) {
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
        'profile_id' => 'required',
        'user_id' => 'required',
        'products' => 'required',
        'total' => 'required'
      ];
    }
}
