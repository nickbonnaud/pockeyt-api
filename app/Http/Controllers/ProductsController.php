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
      $profile = $this->user->profile;
      $products = Product::where('profile_id', '=', $profile->id)->orderBy('name', 'asc')->get();
      foreach ($products as $product) {
        $product->price = ($product->price) / 100;
      }
      return view('products.list', compact('products', 'profile'));
  }

  public function getInventory($id) {
    return $inventory = Product::where('profile_id', '=', $id)->orderBy('name', 'asc')->get();
  }

  public function connectSquare(Request $request) {
    return $this->isLoggedInSquare($request->all());
  }

  public function isLoggedInSquare($data) {
    if ($data['state'] = env('SQUARE_STATE')) return $this->getAccessToken($data['code']);
  }

  public function getAccessToken($code) {
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/oauth2/']);
    try {
      $response = $client->request('POST', 'token', [
        'json' => ['client_id' => env('SQUARE_ID'),
        'client_secret' => env('SQUARE_SECRET'),
        'code'=> $code]
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

  public function syncSquareItems() {
    $squareLocationId = $this->user->profile->account->square_location_id;
    if (isset($squareLocationId)) {
      return $this->syncItems($squareLocationId);
    } else {
      return $this->getSquareLocationId();
    }
  }

  public function getSquareLocationId() {
    try {
      $token = Crypt::decrypt($this->user->profile->square_token);
    } catch (DecryptException $e) {
      dd($e);
    }

    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    try {
      $response = $client->request('GET', 'me/locations', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    $body = json_decode($response->getBody());
    if (count($body) > 1) {
      return $this->matchLocation($body);
    } elseif(count($body) == 1) {
      $account = $this->user->profile->account;
      $account->square_location_id = $body[0]->id;
      $account->save();
      return $this->syncItems($squareLocationId);
    }
  }

  public function syncItems($squareLocationId) {
     try {
      $token = Crypt::decrypt($this->user->profile->square_token);
    } catch (DecryptException $e) {
      dd($e);
    }
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    try {
      $response = $client->request('GET', $squareLocationId . '/items', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }
    $body = json_decode($response->getBody());
    return $this->syncPockeytInventory($body);
  }

  public function matchLocation($locations) {
    $businessLocation = $this->user->profile->account->bizStreetAdress;
    if(isset($businessLocation)) {
      foreach ($locations as $location) {
        if ($location->business_address->address_line_1 == $businessLocation) {
          $account = $this->user->profile->account;
          $account->square_location_id = $location->id;
          $account->save();
          return $this->syncItems($account->square_location_id);
        } 
      }
      flash()->overlay('Oops', "Your business street address in Pockeyt, " . $businessLocation . ", does not match your saved street address in Square. Please change your address in Pockeyt or Square to match in order to continue.", 'error');
      return redirect()->route('products.list');
    } else {
      flash()->overlay('Oops', 'Please set your business address in your Payment Account Info tab in the Your Business Info section.', 'error');
      return redirect()->route('products.list');
    }
  }

  public function syncPockeytInventory($items){
    if ($items === []) {
      flash()->overlay('Oops', "This location has no inventory on Square.", 'error');
      return redirect()->route('products.list');
    } else {
      foreach ($items as $item) {
        $name = $item->name;
        foreach ($item->variations as $variation) {
          $product = Product::where('square_id', '=', $variation->id)->first();
          if (! isset($product)) {
            return $this->createNewProduct($variation, $name);
          } else {
            return $this->updateProduct($variation, $name, $product);
          }
        }
      }
    }
  }

  public function createNewProduct($variation, $name) {
    $product = new Product;

    $product->name = $name . ' ' . $variation->name;
    $product->price = $variation->price_money->amount;
    $product->sku = $variation->sku;
    $product->square_id = $variation->id;
    $this->user->profile->products()->save($product);
    return $this->syncSuccess();
  }

  public function updateProduct($variation, $name, $product) {
    $product->name = $name . ' ' . $variation->name;
    $product->price = $variation->price_money->amount;
    $product->sku = $variation->sku;
    $product->square_id = $variation->id;
    $this->user->profile->products()->save($product);
    return $this->syncSuccess();
  }

  public function syncSuccess() {
    flash()->success('Synced!', 'Pockeyt Inventory updated.');
    return redirect()->route('products.list');
  }

}
