@if(count($inventory) > 0)
  @foreach($inventory as $product)
  	<div class="col-md-3">
      <div class="box-inventory" v-on:click="addProduct()">
        <div class="box-body-inventory">
          @if(! is_null($product->product_photo_path))
            <img src="{{ $product->product_tn_photo_path }}">
          @else
            <img src="{{ asset('/images/noImage.png') }}">
          @endif
        </div>
        <div class="box-footer-inventory">
          <b>{{ str_limit($product->name, 18) }}</b>
        </div>
      </div>
  </div>
  @endforeach
@endif