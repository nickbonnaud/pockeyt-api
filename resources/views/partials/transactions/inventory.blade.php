@if(count($inventory) > 0)
  @foreach($inventory as $product)
  	<div class="col-md-3">
      <div class="box">
        <div class="box-body">
          @if(! is_null($product->product_photo_path))
            <img src="{{ $product->product_tn_photo_path }}" class="product-image">
          @else
            <img src="{{ asset('/images/noImage.png') }}" class="product-image">
          @endif
        </div>
        <div class="box-footer">
          {{ $product->name }}
        </div>
      </div>
  </div>
  @endforeach
@endif