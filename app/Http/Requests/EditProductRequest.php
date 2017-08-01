<?php

namespace App\Http\Requests;
use App\Product;

class EditProductRequest extends Request {
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize() {
  	$user = \Auth::user();
  	return !is_null($product = Product::find($this->route('products')) && ($user->profile->id == $product->profile_id));
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules() {
    return [];
  }
}
