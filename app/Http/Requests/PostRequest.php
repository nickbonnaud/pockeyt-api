<?php

namespace App\Http\Requests;

use App\Profile;
use App\Http\Requests\Request;

class PostRequest extends Request {
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
            'photo' => 'mimes:jpg,jpeg,png,bmp',
            'event_date' => 'date_format: Y-m-d'
        ];
    }
}
