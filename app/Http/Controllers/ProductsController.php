<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Product;
use App\Photo;
use App\Profile;
use Illuminate\Database\Eloquent\Collection;
Use Illuminate\HttpResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Controllers\Controller;

class ProductsController extends Controller {

  /**
   * Create a new PostsController instance
   */
  public function __construct() {
      parent::__construct();
      $this->middleware('auth', ['only' => 'store']);
      $this->middleware('auth:admin', ['only' => ['index']]);
  }

  /**
   * Store new post
   *
   * @param PostRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(ProductRequest $request) {
      $product = new Product($request->all());
      $file = $request->photo;
      
      if($file != null) {
          $photo = Photo::fromForm($file);
          $photo->save();
          $product['product_photo_path'] = url($photo->path);
          $product['photo_id'] = $photo->id;
      }

      $this->user->profile->products()->save($product);
      return redirect()->back();
  }

  public function edit (Request $request, $id) {
  	$product = Product::findOrFail($id);
  	return view('products.edit', compact('product'));
  }

  public function update(UpdateProductRequest $request, $id)
  {
    $product = Product::findOrFail($id);
    $oldPhoto = $product->product_photo_path;
    $updatedProduct = $request->except('photo');
    $file = $request->photo;

    if($file != null) {
      if (isset($oldPhoto)) {
        $photo = Photo::where('id', '=', $product->photo_id);
        $photo->delete();
      }
      $photo = Photo::fromForm($file);
      $photo->save();
      $updatedProduct['product_photo_path'] = url($photo->path);
      $updatedProduct['product_tn_photo_path'] = url($photo->thumbnail_path);
      $updatedProduct['photo_id'] = $photo->id;
    }
  	$product->update($updatedProduct);
  	return redirect()->route('products.edit', compact('product'));
  }

  public function destroy(DeleteProductRequest $request, $id) {
      $product = Product::findOrFail($id);
      $photo = Photo::where('id', '=', $product->photo_id);
      $photo->delete();
      $product->delete();
      return redirect()->back();
  }

  public function listProducts() {
      $products = Product::where('profile_id', '=', $this->user->profile->id)->orderBy('name', 'asc')->get();
      return view('products.list', compact('products'));
  }

  public function getInventory($id) {
    return $inventory = Product::where('profile_id', '=', $id)->orderBy('name', 'asc')->get();
  }


}
