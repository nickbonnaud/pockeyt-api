@extends('layoutDashboard')
@section('content')
<div class="content-wrapper-scroll">
	<div class="scroll-main">
		<div class="scroll-main-contents">
			<section class="content-header">
		    <h1>
		      Refund Center
		    </h1>
		    <ol class="breadcrumb">
		      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
		      <li class="active">Refunds</li>
		    </ol>
		  </section>
		  @include ('errors.form')
			<section class="content" id="refund">
				<div class="input-group input-group-lg invite-code-section">
					<div class="input-group-btn">
						<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">@{{ searchSelection }} <span class="fa fa-caret-down"></span></button>
						<ul class="dropdown-menu">
							<li><a href="#" v-on:click="setSelection('Email')">Customer Email</a></li>
							<li><a href="#" v-on:click="setSelection('ID')">Receipt ID</a></li>
						</ul>
					</div>
					<input v-model="searchInput" name="searchInput" type="search" class="form-control" :disabled="searchSelection == 'Search By'">
					<span class="input-group-btn">
						<button v-on:click="searchReceipts()" type="button" class="btn btn-success btn-flat" :disabled="searchInput == ''">Search</button>
					</span>
				</div>
				<div class="col-md-6">
					<div v-if="selectedReceipt.length > 0">
						<h3 class="learn-bottom">Selected Receipt</h3>
						<div v-for="receipt in selectedReceipt">
							<div class="box box-primary">
				        <div class="box-header with-border">
			        		<h3 class="box-title">@{{ receipt.first_name }} @{{ receipt.last_name }}'s Receipt</h3>
			        		<div class="receipt-date">
				          	@include ('partials.transactions.submit_refund_all')
				          </div>
				          <h4>@{{ receipt.updated_at | setDate }}</h4>
				        </div>
				        <div class="box-body no-padding">
				          <table class="table table-striped" v-if="receipt.deal_id === null">
				            <tbody>
				              <tr>
				              	<th>Refund Item</th>
				                <th>Quantity</th>
				                <th>Name</th>
				                <th class="text-right">Price</th>
				              </tr>
				              <template v-for="product in selectedReceiptItems">
												<tr class="product-row" v-cloak>
													<td class="product-row-data"><span class="glyphicon glyphicon-plus-sign" v-on:click="subtractProduct(product)"></span></td>
													<td class="product-row-data">@{{ product.quantity }}</td>
													<td class="product-row-data">@{{ product.name }}</td>
													<td class="product-row-data text-right">$@{{ (product.price / 100).toFixed(2) }}</td>
												</tr>
											</template>
				            </tbody>
				          </table>
				          <table class="table table-striped" v-else>
				            <tbody>
				              <tr>
				                <th>Quantity</th>
				                <th>Name</th>
				                <th class="text-right">Price</th>
				              </tr>
				              <template>
												<tr class="product-row" v-cloak>
													<td class="product-row-data">1</td>
													<td class="product-row-data">@{{ receipt.products }}</td>
													<td class="product-row-data text-right">$@{{ (receipt.net_sales / 100).toFixed(2) }}</td>
												</tr>
											</template>
				            </tbody>
				          </table>
				        </div>
				        <div class="box-footer-receipt" v-if="receipt.deal_id === null">
				          <div class="tax-section">
				            <span>Tax:</span>
				            <span class="pull-right">$@{{ (totalTax / 100).toFixed(2) }}</span>
				          </div>
				          <div class="tax-section">
				            <span>Tip:</span>
				            <span class="pull-right">$@{{ (receipt.tips / 100).toFixed(2) }}</span>
				          </div>
				          <b>Total:</b>
				          <div class="receipt-total">
				            <b>$@{{ ((totalBill + receipt.tips) / 100).toFixed(2) }}</b>
				          </div>
				        </div>
				        <div class="box-footer-receipt" v-else>
				          <div class="tax-section">
				            <span>Tax:</span>
				            <span class="pull-right">$@{{ (receipt.tax / 100).toFixed(2) }}</span>
				          </div>
				          <b>Total:</b>
				          <div class="receipt-total">
				            <b>$@{{ (receipt.total / 100).toFixed(2) }}</b>
				          </div>
				        </div>
				      </div>
						</div>
					</div>
				</div>
				<div class="scroll-container-analytics">
					<div class="scroll-contents">
						<div v-show="(searchResult.length == 0) && (selectedReceipt.length == 0)">
							<h3 class="learn-bottom">Recent Transactions</h3>
							<div v-for="receipt in receipts">
								<div class="box box-black">
					        <div class="box-header with-border">
				        		<h3 class="box-title">@{{ receipt.first_name }} @{{ receipt.last_name }}'s Receipt</h3>
				        		<div class="receipt-date">
					          	<button v-on:click="selectReceipt(receipt)" class="btn btn-block btn-success btn-xs">Select Receipt</button>
					          </div>
					          <h4>@{{ receipt.updated_at | setDate }}</h4>
					        </div>
					        <div class="box-body no-padding">
					          <table class="table table-striped" v-if="receipt.deal_id === null">
					            <tbody>
					              <tr>
					                <th>Quantity</th>
					                <th>Name</th>
					                <th class="text-right">Price</th>
					              </tr>
					              <template v-for="product in billItems(receipt)">
													<tr class="product-row" v-cloak>
														<td class="product-row-data">@{{ product.quantity }}</td>
														<td class="product-row-data">@{{ product.name }}</td>
														<td class="product-row-data text-right">$@{{ (product.price / 100).toFixed(2) }}</td>
													</tr>
												</template>
					            </tbody>
					          </table>
					          <table class="table table-striped" v-else>
					            <tbody>
					              <tr>
					                <th>Quantity</th>
					                <th>Name</th>
					                <th class="text-right">Price</th>
					              </tr>
					              <template>
													<tr class="product-row" v-cloak>
														<td class="product-row-data">1</td>
														<td class="product-row-data">@{{ receipt.products }}</td>
														<td class="product-row-data text-right">$@{{ (receipt.net_sales / 100).toFixed(2) }}</td>
													</tr>
												</template>
					            </tbody>
					          </table>
					        </div>
					        <div class="box-footer-receipt">
					          <div class="tax-section">
					            <span>Tax:</span>
					            <span class="pull-right">$@{{ (receipt.tax / 100).toFixed(2) }}</span>
					          </div>
					          <div class="tax-section">
					            <span>Tip:</span>
					            <span class="pull-right">$@{{ (receipt.tips / 100).toFixed(2) }}</span>
					          </div>
					          <b>Total:</b>
					          <div class="receipt-total">
					            <b>$@{{ (receipt.total / 100).toFixed(2) }}</b>
					          </div>
					        </div>
					      </div>
							</div>
						</div>
						<div v-show="(searchResult.length > 0) && (typeof searchResult != 'string') && (selectedReceipt.length == 0)">
							<h3 class="learn-bottom">Search Results</h3>
							<div v-for="receipt in searchResult">
								<div class="box box-black">
					        <div class="box-header with-border">
				        		<h3 class="box-title">@{{ receipt.first_name }} @{{ receipt.last_name }}'s Receipt</h3>
				        		<div class="receipt-date">
					          	<button v-on:click="selectReceipt(receipt)" class="btn btn-block btn-success btn-xs">Select Receipt</button>
					          </div>
					          <h4>@{{ receipt.updated_at | setDate }}</h4>
					        </div>
					        <div class="box-body no-padding">
					          <table class="table table-striped" v-if="receipt.deal_id === null">
					            <tbody>
					              <tr>
					                <th>Quantity</th>
					                <th>Name</th>
					                <th class="text-right">Price</th>
					              </tr>
					              <template v-for="product in billItems(receipt)">
													<tr class="product-row" v-cloak>
														<td class="product-row-data">@{{ product.quantity }}</td>
														<td class="product-row-data">@{{ product.name }}</td>
														<td class="product-row-data text-right">$@{{ (product.price / 100).toFixed(2) }}</td>
													</tr>
												</template>
					            </tbody>
					          </table>
					          <table class="table table-striped" v-else>
					            <tbody>
					              <tr>
					                <th>Quantity</th>
					                <th>Name</th>
					                <th class="text-right">Price</th>
					              </tr>
					              <template>
													<tr class="product-row" v-cloak>
														<td class="product-row-data">1</td>
														<td class="product-row-data">@{{ receipt.products }}</td>
														<td class="product-row-data text-right">$@{{ (receipt.net_sales / 100).toFixed(2) }}</td>
													</tr>
												</template>
					            </tbody>
					          </table>
					        </div>
					        <div class="box-footer-receipt">
					          <div class="tax-section">
					            <span>Tax:</span>
					            <span class="pull-right">$@{{ (receipt.tax / 100).toFixed(2) }}</span>
					          </div>
					          <div class="tax-section">
					            <span>Tip:</span>
					            <span class="pull-right">$@{{ (receipt.tips / 100).toFixed(2) }}</span>
					          </div>
					          <b>Total:</b>
					          <div class="receipt-total">
					            <b>$@{{ (receipt.total / 100).toFixed(2) }}</b>
					          </div>
					        </div>
					      </div>
							</div>
						</div>
						<div v-show="refundReceiptActive === true">
							<h3 class="learn-bottom">Items for Refund</h3>
							<div class="box box-black">
				        <div class="box-header with-border">
			        		<h3 class="box-title">Refund Receipt</h3>
			        		<div class="receipt-date">
				          	<button v-on:click="resetReceipt()" class="btn btn-block btn-danger btn-xs">Clear</button>
				          </div>
				          <h4>@{{ new Date() | setDate }}</h4>
				        </div>
				        <div class="box-body no-padding">
				          <table class="table table-striped">
				            <tbody>
				              <tr>
				                <th>Quantity</th>
				                <th>Name</th>
				                <th class="text-right">Price</th>
				              </tr>
				              <template v-for="product in refundReceiptItems">
												<tr class="product-row" v-if="product.quantity !== 0" v-cloak>
													<td class="product-row-data">@{{ product.quantity }}</td>
													<td class="product-row-data">@{{ product.name }}</td>
													<td class="product-row-data text-right">$@{{ (product.price / 100).toFixed(2) }}</td>
												</tr>
											</template>
				            </tbody>
				          </table>
				        </div>
				        <div class="box-footer-receipt">
				          <div class="tax-section">
				            <span>Tax:</span>
				            <span class="pull-right">$@{{ (totalTaxRefund / 100).toFixed(2) }}</span>
				          </div>
				          <b>Total:</b>
				          <div class="receipt-total">
				            <b>$@{{ (totalBillRefund / 100).toFixed(2) }}</b>
				          </div>
				          @include ('partials.transactions.submit_refund_partial')
				        </div>
				      </div>
						</div>
						<div v-show="(searchResult.length > 0) && (typeof searchResult == 'string')">
							<h3 class="learn-bottom">No Results</h3>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>


@stop
@section('scripts.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script>
	var refund = new Vue({
		el: '#refund',

		data: {
			receipts: {!! $transactions !!},
			searchSelection: "Search By",
			searchInput: '',
			searchResult: [],
			selectedReceipt: [],
			selectedReceiptId: '',
			selectedReceiptItems: [],
			refundReceiptItems: [],
			refundReceiptActive: false
		},

		filters: {
			setDate: function(value) {
				date = moment(value).format("MMM Do YY");
				return date;
			}
		},

		computed: {
			subTotal: function() {
        var bill = this.selectedReceiptItems;
        var total = 0;
        bill.forEach(function(product) {
          total = total + (product.quantity * product.price)
        });
        return total;
      },
      subTotalRefund: function() {
        var bill = this.refundReceiptItems;
        var total = 0;
        bill.forEach(function(product) {
          total = total + (product.quantity * product.price)
        });
        return total;
      },
      totalTax: function() {
        var tax = this.subTotal * {{ ($profile->tax_rate) / 10000 }};
        return tax;
      },
      totalTaxRefund: function() {
        var tax = this.subTotalRefund * {{ ($profile->tax_rate) / 10000 }};
        return tax;
      },
      totalBill: function() {
        var total = this.subTotal + this.totalTax;
        return total;
      },
      totalBillRefund: function() {
        var total = this.subTotalRefund + this.totalTaxRefund;
        return total;
      }
		},

		methods: {
			addProductToRefund: function(product) {
				console.log(product);
			},
			billItems: function(receipt) {
				return JSON.parse(receipt.products);
			},
			setSelection: function(selection) {
				this.searchSelection = selection;
			},
			searchReceipts: function() {
				this.searchResult = [];
				this.selectedReceipt = [];
				this.selectedReceiptItems = [];
				this.refundReceiptItems = [];
				this.selectedReceiptId = '';
				this.refundReceiptActive = false;
				$.ajax({
					method: 'POST',
					url: '/refunds/search',
					data: {
						'searchSelection': this.searchSelection,
						'searchInput' : this.searchInput,
						'businessId' : {!! $profile->id !!}
					},
					success: function(data) {
						if (data == 'Not Found') {
							console.log(data);
							refund.$data.searchResult = data;
						} else {
							console.log(data);
							data.forEach(function(receipt) {
								refund.$data.searchResult.push(receipt);
							});
						}
					}
				})
			},
			selectReceipt: function(receipt) {
				this.selectedReceipt = [];
				this.selectedReceiptId = receipt.id;
				this.selectedReceiptItems = JSON.parse(receipt.products);
				this.selectedReceipt.push(receipt);
				this.setRefundReceiptItems(receipt);
			},
			setRefundReceiptItems: function(receipt) {
				this.refundReceiptItems = [];
				this.refundReceiptItems = JSON.parse(receipt.products);
				var refundReceiptItems = this.refundReceiptItems;
				for(var i = 0; i < refundReceiptItems.length; i++) {
          refundReceiptItems[i].quantity = 0; 
        }
			},
			refundAll: function(receipt) {
				console.log(receipt);
			},
			subtractProduct: function(product) {
        var selectedReceiptItems = this.selectedReceiptItems;
        var result = $.grep(selectedReceiptItems, function(item) { return item.id === product.id});
        if (result[0].quantity !== 1) {
        	var refundItem = result[0];
          this.addToRefundReceipt(refundItem);
          result[0].quantity--
        } else {
          for(var i = 0; i < selectedReceiptItems.length; i++) {
            if(selectedReceiptItems[i].id == product.id) {
            	var refundItem = selectedReceiptItems[i];
            	this.addToRefundReceipt(refundItem);
              selectedReceiptItems.splice(i, 1);
              break;
            }
          }
        }
      },
      addToRefundReceipt: function(refundItem) {
      	var refundReceiptItems = this.refundReceiptItems;
      	var result = $.grep(refundReceiptItems, function(item) { return item.id === refundItem.id});
      	result[0].quantity++
      	this.refundReceiptActive = true;
      },
      resetReceipt: function() {
      	var receipt = this.selectedReceipt[0];
      	this.selectedReceiptItems = JSON.parse(receipt.products);
      	this.setRefundReceiptItems(receipt);
      	this.refundReceiptActive = false;
      }
		}
	})


</script>
@stop




