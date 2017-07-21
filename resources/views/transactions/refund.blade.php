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
				<div class="scroll-container-analytics">
					<div class="scroll-contents">
						<div class="col-md-6">
							
						</div>

						<div class="col-md-6">
							<div v-for="receipt in receipts">
								<div class="box box-black">
					        <div class="box-header with-border">
					        	<div class="row">
					        		<h3 class="box-title">@{{ receipt.first_name }} @{{ receipt.last_name }}'s Receipt</h3>
					        		<div class="receipt-date">
						          	<button class="btn btn-block btn-success btn-xs">Refund All</button>
						          </div>
					        	</div>
					          <h4>@{{ receipt.updated_at | setDate }}</h4>
					        </div>
					        <div class="box-body no-padding">
					          <table class="table table-striped">
					            <tbody>
					              <tr>
					                <th>Quantity</th>
					                <th>Name</th>
					                <th>Price</th>
					                <th></th>
					              </tr>
					              <template v-for="product in billItems(receipt)">
													<tr class="product-row" v-cloak>
														<td class="product-row-data">@{{ product.quantity }}</td>
														<td class="product-row-data">@{{ product.name }}</td>
														<td class="product-row-data">$@{{ (product.price / 100).toFixed(2) }}</td>
														<td class="product-row-data"><span class="glyphicon glyphicon-plus-sign" v-on:click="addProductToRefund(product)"></span></td>
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
					          <b>Total:</b>
					          <div class="receipt-total">
					            <b>$@{{ (receipt.total / 100).toFixed(2) }}</b>
					          </div>
					        </div>
					      </div>
							</div>
						</div>

						<div class="col-md-6">
							
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
			receipts: {!! $transactions !!}
		},

		filters: {
			setDate: function(value) {
				date = moment(value).format("MMM Do YY");
				return date;
			}
		},

		methods: {
			addProductToRefund: function(product) {
				console.log(product);
			},
			billItems: function(receipt) {
				console.log(receipt.products);
				return JSON.parse(receipt.products);
			}
		}

	})


</script>
@stop




