@if(count($products) > 0)
  @foreach($products as $product)
  	<tr class="product-row">
  		<td class="product-row-data"><a href="{{ route('products.edit', ['product' => $product->id]) }}">{{ $product->name }}</a></td>
  		<td class="product-row-data">${{ $product->price }}</td>
  		@if(! is_null($product->product_photo_path))
  			<td><img src="{{ $product->product_tn_photo_path }}" class="product-image"></td>
      @else
        <td><img src="{{ asset('/images/noImage.png') }}" class="product-image"></td>
  		@endif
  		<td>@include('partials.products.delete')</td>
  	</tr>
  @endforeach
@endif