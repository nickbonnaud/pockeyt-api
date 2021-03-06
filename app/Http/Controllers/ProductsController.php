<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\EditProductRequest;
use App\Http\Requests\ListProductsRequest;
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
      $product = new Product($request->except('price'));
      $file = $request->photo;
      
      if($file != null) {
          $photo = Photo::fromForm($file);
          $photo->save();
          $product['product_photo_path'] = url($photo->path);
          $product['product_tn_photo_path'] = url($photo->thumbnail_path);
          $product['photo_id'] = $photo->id;
      }
      $product->price = preg_replace("/[^0-9\.]/","",$request->price)  * 100;
      $this->user->profile->products()->save($product);
      return redirect()->back();
  }

  public function edit (EditProductRequest $request, $id) {
  	$product = Product::findOrFail(Crypt::decrypt($id));
    $profile = $this->user->profile;
    $categories = Product::where('profile_id', '=', $profile->id)->whereNotNull('category')->select('category')->distinct()->get();
    $product->price = $product->price / 100;
  	return view('products.edit', compact('product', 'categories'));
  }

  public function update(UpdateProductRequest $request, $id)
  {
    $product = Product::findOrFail($id);

    $oldPhoto = $product->product_photo_path;
    $updatedProduct = $request->except('photo', 'price');
    $updatedProduct['price'] = preg_replace("/[^0-9\.]/","",$request->price)  * 100;
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

    $profile = $this->user->profile;
    $products = Product::where('profile_id', '=', $profile->id)->orderBy('name', 'asc')->get();
      foreach ($products as $product) {
        $product->price = ($product->price) / 100;
      }
  	return redirect()->route('products.list', ['profiles' => Crypt::encrypt($profile->id)]);
  }

  public function destroy(DeleteProductRequest $request, $id) {
      $product = Product::findOrFail($id);
      $photo = Photo::where('id', '=', $product->photo_id);
      $photo->delete();
      $product->delete();
      return redirect()->back();
  }

  public function listProducts(ListProductsRequest $request, $id) {
      $profile = Profile::find(Crypt::decrypt($id));
      $products = Product::where('profile_id', '=', $profile->id)->orderBy('name', 'asc')->get();
      foreach ($products as $product) {
        $product->price = ($product->price) / 100;
      }
      return view('products.list', compact('products', 'profile'));
  }

  public function getInventory($id) {
    return $inventory = Product::where('profile_id', '=', $id)->orderBy('name', 'asc')->get();
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

    $client = new Client([
      'base_url' => ['https://connect.squareup.com/{version}/', ['version' => 'v1']]
    ]);

    try {
      $response = $client->get('me/locations', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch(RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }


    // $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    // try {
    //   $response = $client->request('GET', 'me/locations', [
    //     'headers' => [
    //       'Authorization' => 'Bearer ' . $token,
    //       'Accept' => 'application/json'
    //     ]
    //   ]);
    // } catch (RequestException $e) {
    //   if ($e->hasResponse()) {
    //     return $e->getResponse();
    //   }
    // }


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
    $client = new Client([
      'base_url' => ['https://connect.squareup.com/{version}/', ['version' => 'v1']]
    ]);

    try {
      $response = $client->get($squareLocationId . '/items', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch(RequestException $e) {
      if ($e->hasResponse()) {
        return $e->getResponse();
      }
    }


    // $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
    // try {
    //   $response = $client->request('GET', $squareLocationId . '/items', [
    //     'headers' => [
    //       'Authorization' => 'Bearer ' . $token,
    //       'Accept' => 'application/json'
    //     ]
    //   ]);
    // } catch (RequestException $e) {
    //   if ($e->hasResponse()) {
    //     return $e->getResponse();
    //   }
    // }

    
    $body = json_decode($response->getBody());
    return $this->syncPockeytInventory($body);
  }

  public function matchLocation($locations) {
    $businessLocation = $this->user->profile->account->bizStreetAdress;
    $profile = $this->user->profile;
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
      return redirect()->route('products.list', ['profiles' => Crypt::encrypt($profile->id)]);
    } else {
      flash()->overlay('Oops! Please finish your account', 'Set your business address in the Payment Account Info tab in the Business Info section.', 'error');
      return redirect()->route('products.list', ['profiles' => Crypt::encrypt($profile->id)]);
    }
  }

  public function syncPockeytInventory($items){
    if ($items === []) {
      flash()->overlay('Oops', "This location has no inventory on Square.", 'error');
      $profile = $this->user->profile;
      return redirect()->route('products.list', ['profiles' => Crypt::encrypt($profile->id)]);
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
    $profile = $this->user->profile;
    return redirect()->route('products.list', ['profiles' => Crypt::encrypt($profile->id)]);
  }

}
