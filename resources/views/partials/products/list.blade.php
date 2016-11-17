@if(count($products) > 0)
  @foreach($products as $product)
  	<tr>
  		<td><a href="{{ route('products.edit', ['product' => $product->id]) }}">{{ $product->name }}</a></td>
  		<td>${{ $product->price }}</td>
  		@if(! is_null($product->product_photo_path))
  			<td><img src="{{ $product->product_photo_path }}" class="product-image"></td>
  		@endif
  	</tr>
  @endforeach
@endif