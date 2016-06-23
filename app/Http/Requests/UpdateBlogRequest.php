<?php

namespace App\Http\Requests;

class UpdateBlogRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return !is_null($user = \Auth::user()) && ($user->is_admin);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return with(new BlogRequest())->rules();
    }
}
