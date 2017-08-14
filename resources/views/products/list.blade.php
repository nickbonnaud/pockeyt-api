@extends('layoutDashboard')

@section('content')
<div class="content-wrapper-scroll" id="product">
  <div class="scroll-main">
    <div class="scroll-main-contents">
    	<section class="content-header">
        <h1>
          Current Inventory
        </h1>
        <div class="auto-post">
          @if(isset($profile->square_token))
            <a href="{{ action('ProductsController@syncSquareItems') }}">
              <button type="button" class="btn btn-social btn-github">
                <i class="fa fa-sign-in"></i>
                Sync
              </button>
            </a>
          @else
            <a href="{{ 'https://connect.squareup.com/oauth2/authorize?client_id=' . env('SQUARE_ID') . '&scope=ITEMS_READ%20ITEMS_WRITE%20MERCHANT_PROFILE_READ%20PAYMENTS_READ&state=' . env('SQUARE_STATE') }}">
              <button type="button" class="btn btn-social btn-github">
                <i class="fa fa-sign-in"></i>
                Sync Square Inventory
              </button>
            </a>
          @endif
        </div>
        <ol class="breadcrumb">
          <li><a href="{{ route('profiles.show', ['profiles' => Crypt::encrypt($user->profile->id)]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Inventory</li>
        </ol>
      </section>
    	<section class="content">
      	<div class="scroll-container-analytics col-md-12">
          <div class="scroll-contents">
        		<div class="box box-primary">
        			<div class="box-header with-border">
        				<h3 class="box-title-inventory">Products</h3>
        				<div class="pull-right">
        				<button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#addProductModal">Add</button>
        				</div>
        			</div>
        			<div class="box-body no-padding">
        				<table class="table table-striped">
        					<tbody>
        						<tr>
        							<th>Name</th>
        							<th>Price</th>
        							<th>Photo</th>
        							<th width="70px"></th>
        						</tr>
        						@include('partials.products.list', ['products' => $products])
        					</tbody>
        				</table>
        			</div>
        		</div>
          </div>
      	</div>
    	</section>
    </div>
  </div>
  <div class="modal fade" id="addProductModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header-timeline">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="addProductModal">Add new product</h4>
        </div>
        <div class="modal-body-customer-info">
          {!! Form::open(['method' => 'POST', 'route' => ['products.store'], 'files' => true]) !!}
            @include ('partials.products.form')
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
</div>
@stop
@section('scripts.footer')
<script>

  var product = new Vue({
    el: '#product',

    components: {
      VMoney
    },

    data: {
      price: '',
      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false
      }
    }
  });

  getCategories = function() {
    var products = {!! $products !!};
    var categories = [];
    products.forEach(function(product) {
      if (product.category) {
        if (categories.indexOf(product.category) == -1) {
          categories.push(product.category);
        }
      }
    });
    return categories;
  };

  $("#category").select2({
    width: '100%',
    tags: true,
    data: getCategories(),
    maximumSelectionLength: 1
  });

</script>
@stop