<div v-if="inventory.length > 0" >
	<div class="col-md-3 col-sm-4 col-xs-2" v-for="product in productsFilter">
    <div class="box-inventory" v-on:click="addProduct(product)">
      <div class="box-body-inventory">
        <img v-if="product.product_tn_photo_path" :src="product.product_tn_photo_path">
        <img v-else src="{{ asset('/images/noImage.png') }}">
      </div>
      <div class="box-footer-inventory">
        <b>@{{ product.name | truncate}}</b>
      </div>
    </div>
  </div>
</div>