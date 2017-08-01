<?php

namespace App\Http\Requests;

class EditProductRequest extends Request {
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize() {
  	$product = \Product::find($this->route('products'));
  	$profileId = $produt->profile_id;
  	$user = \Auth::user();
  	return $user->profile->id == $profileId;
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
