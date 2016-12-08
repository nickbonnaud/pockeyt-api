<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TransactionRequest extends Request {
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
            'profile_id' => 'required',
            'user_id' => 'required',
            'products' => 'required',
            'total' => 'required'
        ];
    }
}
