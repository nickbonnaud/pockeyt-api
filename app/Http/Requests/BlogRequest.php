<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class BlogRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'author' => 'required',
            'description' => 'required',
            'blog_title' => 'required',
            'blog_body' => 'required',
            'blog_hero' => 'required|mimes:jpg,jpeg,png,bmp',
            'blog_profile' => 'required|mimes:jpg,jpeg,png,bmp'
        ];
    }
}
