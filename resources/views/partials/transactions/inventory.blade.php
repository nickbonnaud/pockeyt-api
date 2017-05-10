<div v-if="inventory.length > 0" >
	<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" v-for="product in productsFilter">
    <div v-if="product.product_tn_photo_path" class="box-inventory" v-on:click="addProduct(product)">
      <div class="box-body-inventory-image">
        <img :src="product.product_tn_photo_path">
      </div>
      <div class="box-footer-inventory">
        <b>@{{ product.name | truncate }}</b>
      </div>
    </div>
    <div v-else class="box-inventory" v-on:click="addProduct(product)">
      <div class="box-body-inventory">
        <p class="inventory-text"><strong>@{{ product.name | truncateLong }}</strong></p>
      </div>
      <div class="box-footer-inventory">
        <b>Add</b>
      </div>
    </div>
  </div>
</div>