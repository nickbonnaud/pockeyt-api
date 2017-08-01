<?php

namespace App\Http\Requests;
use App\Product;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EditProductRequest extends Request {
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize() {
  	$user = \Auth::user();
    try {
      return !is_null($product = Product::find(Crypt::decrypt($this->route('products')))) && ($user->profile->id == $product->profile_id);
    } catch(DecryptException $e) {
      return false;
    }
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
