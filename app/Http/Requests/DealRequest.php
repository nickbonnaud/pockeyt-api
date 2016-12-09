<?php

namespace App\Http\Requests;

use App\Profile;
use App\Http\Requests\Request;

class DealRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'message' => 'required',
            'is_redeemable' => 'required',
            'photo' => 'mimes:jpg,jpeg,png,bmp',
            'price' => 'required',
            'end_date' => 'date_format: Y-m-d'
        ];
    }
}
