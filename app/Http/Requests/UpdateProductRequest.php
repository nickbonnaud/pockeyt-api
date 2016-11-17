<?php

namespace App\Http\Requests;

class UpdateProductRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $isAuthorized = false;
        if (!is_null($user = \Auth::user())) {
            $user = \Auth::user();
            foreach ($user->profile->products as $product) {
                if ($product->id == $this->route('products')) {
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
        'name' => 'required',
        'price' => 'required',
        'photo' => 'mimes:jpg,jpeg,png,bmp',
      ];
    }
}
