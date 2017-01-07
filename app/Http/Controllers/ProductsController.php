<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Product;
use App\Photo;
use App\Profile;
use Crypt;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
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
    $this->middleware('auth');
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
          $product['product_tn_photo_path'] = url($photo->thumbnail_path);
          $product['photo_id'] = $photo->id;
      }
      $product->price = $product->price * 100;
      $this->user->profile->products()->save($product);
      return redirect()->back();
  }

  public function edit (Request $request, $id) {
  	$product = Product::findOrFail($id);
    $product->price = $product->price / 100;
  	return view('products.edit', compact('product'));
  }

  public function update(UpdateProductRequest $request, $id)
  {
    $product = Product::findOrFail($id);

    $oldPhoto = $product->product_photo_path;
    $updatedProduct = $request->except('photo');
    $updatedProduct['price'] = $updatedProduct['price'] * 100;
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
      foreach ($products as $product) {
        $product->price = ($product->price) / 100;
      }
      return view('products.list', compact('products'));
  }

  public function getInventory($id) {
    return $inventory = Product::where('profile_id', '=', $id)->orderBy('name', 'asc')->get();
  }

  public function connectSquare(Request $request) {
    return $this->isLoggedInSquare($request->all());
  }

  public function isLoggedInSquare($data) {
    if (! $data) return $this->getAuthorization();
    if ($data->state = env('SQUARE_STATE')) return $this->getAccessToken($data->code);
  }

  public function getAuthorization() {
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/oauth2/'], ['headers' => [
        'Access-Control-Allow-Origin' => '*',
        'Authorization'  => 'Client ' . env('SQUARE_SECRET'),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
      ]]);
    dd($client);
    try {
      $response = $client->request('GET', 'authorize', [
        'query' => ['client_id' => env('SQUARE_ID'), 'scope' => 'ITEMS_READ', 'state' => env('SQUARE_STATE')]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    return $response;
  }

  public function getAccessToken($code) {
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/oauth2'], ['headers' => [
        'Authorization'  => 'Client ' . env('SQUARE_SECRET'),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
      ]]);
    try {
      $response = $client->request('POST', '/token', [
        'json' => ['client_id' => env('SQUARE_ID'), 'client_secret' => env('SQUARE_SECRET'), 'code'=> $code]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    $body = json_decode($response->getBody());
    $profile = $this->user->profile;
    $profile->square_token = Crypt::encrypt($body->access_token);
    $profile->save();
    flash()->success('Connected!', 'You can now import inventory from Square');
    return redirect()->route('products.list');
  }

}
