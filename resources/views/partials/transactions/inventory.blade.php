<div v-if="inventory.length > 0" >
	<div class="col-md-3 col-sm-6 col-xs-6" v-for="product in productsFilter">
    <div class="box-inventory" v-on:click="addProduct(product)">
      <div class="box-body-inventory">
        <img v-if="product.product_tn_photo_path" :src="product.product_tn_photo_path">
        <p v-else class="inventory-text"><strong>@{{ product.name | truncateLong }}</strong></p>
      </div>
      <div class="box-footer-inventory">
        <b v-if="product.product_tn_photo_path">@{{ product.name | truncate }}</b>
        <b v-else>Add</b>
      </div>
    </div>
  </div>
</div>