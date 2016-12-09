@extends('layoutDashboard')

@section('content')
<div id="wrapper">
	<div class="content-wrapper-scroll">
		<div class="scroll-main">
			<div class="scroll-main-contents">
				<section class="content-header">
			    <h1>
			      Add | Active Deals
			    </h1>
			    <ol class="breadcrumb">
			      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
			      <li class="active">Deals</li>
			    </ol>
			  </section>
				<section class="content">
					<div class="col-md-6">
						<div class="box box-primary">
							<div class="box-header with-border">
								<h3 class="box-title">Create a New Deal</h3>
							</div>
								@include ('errors.form')
								{!! Form::open(['method' => 'POST', 'route' => ['deals.store'], 'files' => true]) !!}
								@include('partials.posts.deal_form')
								{!! Form::close() !!}
						</div>
					</div>
					<div class="scroll-container col-md-6">
						<div class="scroll-contents">
							@include('partials.posts.deals', ['posts' => $posts, 'no_icons' => true])
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>

	<div class="modal fade" id="dealModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header-timeline">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="dealModal">Fresh stats just for you!</h4>
	      </div>
	      <div class="modal-body-deals">
	      	<div class="box-body">
	      		<div class="col-md-6 col-sm-12 col-xs-12">
							<div class="info-box">
								<span class="info-box-icon bg-green">
									<i class="fa fa-smile-o"></i>
								</span>
								<div class="info-box-content">
									<span class="info-box-text">Redeemed</span>
									<span class="info-box-number">@{{ redeemed }}</span>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="info-box">
								<span class="info-box-icon bg-aqua">
									<i class="fa fa-bolt"></i>
								</span>
								<div class="info-box-content">
									<span class="info-box-text">Purchased</span>
									<span class="info-box-number">50</span>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-12 col-xs-12">
							<div class="info-box">
								<span class="info-box-icon bg-yellow">
									<i class="fa fa-hourglass-o"></i>
								</span>
								<div class="info-box-content">
									<span class="info-box-text">Outstanding</span>
									<span class="info-box-number">30</span>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-12 col-xs-12">
							<div class="info-box">
								<span class="info-box-icon bg-green">
									<i class="fa fa-dollar"></i>
								</span>
								<div class="info-box-content">
									<span class="info-box-text">Earned</span>
									<span class="info-box-number">$100</span>
								</div>
							</div>
						</div>
	      	</div>
	    	</div>
	  	</div>
		</div>
	</div>
</div>
@stop

@section('scripts.footer')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>

	<script>
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

		$(function() {
      $( "#end_date_pretty" ).datepicker({
          dateFormat: "DD, d MM, yy",
          altField: "#end_date",
          altFormat: "yy-mm-dd"
      });
    });


		var wrapper = new Vue({
			el: "#wrapper",

			data: {
				purchasedDeals: []
			},

			computed: {
				redeemed: function() {
					var count = 0;
					this.purchasedDeals.forEach(function(e) {
						if (e.redeemed === true) {
						}
					});
					console.log(count);
					return count;
				}
			},

			methods: {
				getPurchasedDeals: function(postId) {
					$.ajax({
						method: 'POST',
						url: '/purchased/deals',
						data: {
							'postId' : postId
						},
						success: function(data) {
							this.purchasedDeals = data;
						}
					})
				}
			}
		})




	</script>

@stop

