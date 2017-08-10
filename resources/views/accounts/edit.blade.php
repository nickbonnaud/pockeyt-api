@extends('layoutDashboard')
@section('content')
<?php
$qbo_obj = new \App\Http\Controllers\QuickBookController();
$qbo_connect = $qbo_obj->qboConnect();
?>
<div class="content-wrapper-scroll" id="account">
	<div class="scroll-main">
		<div class="scroll-main-contents">
			<section class="content-header">
		    <h1>
		      Your Business Account Profile
		    </h1>
		     @if(!$qbo_connect)
		    	<span class="pull-right" style="margin-top: -5px;">
						<ipp:connectToIntuit></ipp:connectToIntuit>
					</span>
				@else
					<span class="pull-right" style="margin-top: -5px;">
						<a href="{{ action('QuickBookController@qboDisconnect') }}">Disconnect QuickBooks</a>
					</span>
				@endif
				@if(!$account->pockeyt_qb_taxcode && $qbo_connect)
					<a href="{{ action('QuickBookController@setTaxRate') }}"><button class="btn btn-warning">Set Sales Tax</button></a>
				@endif
		    @if($account->status == 'pending' || $account->status == 'review')
		    	<p><i class="fa fa-circle text-warning"></i> Payment Account Pending</p>
		    @elseif($account->status == 'active')
		    	<p><i class="fa fa-circle text-success"></i> Payment Account Active</p>
		    @else
		    	<p><i class="fa fa-circle text-danger"></i> Payment Account Not Approved</p>
		    	<p>{{ $account->status }}</p>
		    @endif
		    <ol class="breadcrumb">
		      <li><a href="{{ route('profiles.show', ['profiles' => Crypt::encrypt($user->profile->id)]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
		      <li class="active">Payment Account Info</li>
		    </ol>
		  </section>
		  @include ('errors.form')
			<section class="content">
				<div class="scroll-container-analytics">
					<div class="scroll-contents">
						<div class="col-md-6">
							<div class="box box-primary">
								<div class="box-header with-border">
									<h3 class="box-title">Individual Account Holder Info</h3>
								</div>
								<div class="box-body">
									<ul class="list-group list-group-unbordered">
										<li class="list-group-item">
											<b>First Name</b>
											<p class="pull-right">{{ $account->accountUserFirst }}</p>
										</li>
										<li class="list-group-item">
											<b>Last Name</b>
											<p class="pull-right">{{ $account->accountUserLast }}</p>
										</li>
										<li class="list-group-item">
											<b>Email</b>
											<p class="pull-right">{{ $account->ownerEmail }}</p>
										</li>
										<li class="list-group-item">
											<b>Date of Birth</b>
											<p class="pull-right">{{ $account->dateOfBirth }}</p>
										</li>
										<li class="list-group-item">
											<b>SSN last 4</b>
											<p class="pull-right">{{ $account->ssn }}</p>
										</li>
										<li class="list-group-item">
											<b>Percentage Ownership</b>
											<p class="pull-right">{{ $account->ownership / 100 }}%</p>
										</li>
										<li class="list-group-item">
											<b>Street Address</b>
											<p class="pull-right">{{ $account->indivStreetAdress }}</p>
										</li>
										<li class="list-group-item">
											<b>City</b>
											<p class="pull-right">{{ $account->indivCity }} </p>
										</li>
										<li class="list-group-item">
											<b>State</b>
											<p class="pull-right">{{ $account->indivState }}</p>
										</li>
										<li class="list-group-item">
											<b>Zip</b>
											<p class="pull-right">{{ $account->indivZip }}</p>
										</li>
									</ul>
									<a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#individualAccountInfoModal">
				          	<b>Change</b>
				        	</a>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="box box-primary">
								<div class="box-header with-border">
									<h3 class="box-title">Business Info</h3>
								</div>
								<div class="box-body">
									<ul class="list-group list-group-unbordered">
										<li class="list-group-item">
											<b>Legal Business Name</b>
											<p class="pull-right">{{ $account->legalBizName }}</p>
										</li>
										<li class="list-group-item">
											<b>Legal Business Name</b>
											@if($account->businessType == 0)
												<p class="pull-right">Sole Proprietor</p>
											@elseif($account->businessType == 2)
												<p class="pull-right">LLC</p>
											@elseif($account->businessType == 3)
												<p class="pull-right">Partnership</p>
											@elseif($account->businessType == 1)
												<p class="pull-right">Corporation</p>
											@elseif($account->businessType == 4)
												<p class="pull-right">Association</p>
											@endif
										</li>
										<li class="list-group-item">
											<b>Business Tax ID (EIN)</b>
											<p class="pull-right">{{ $account->bizTaxId }}</p>
										</li>
										<li class="list-group-item">
											<b>Date Business Established</b>
											<p class="pull-right">{{ $account->established }}</p>
										</li>
										<li class="list-group-item">
											<b>Estimated Annual Credit Card Sales</b>
											<p class="pull-right">${{ $account->annualCCSales }}</p>
										</li>
										<li class="list-group-item">
											<b>Street Address</b>
											<p class="pull-right">{{ $account->bizStreetAdress }}</p>
										</li>
										<li class="list-group-item">
											<b>City</b>
											<p class="pull-right">{{ $account->bizCity }} </p>
										</li>
										<li class="list-group-item">
											<b>State</b>
											<p class="pull-right">{{ $account->bizState }}</p>
										</li>
										<li class="list-group-item">
											<b>Zip</b>
											<p class="pull-right">{{ $account->bizZip }}</p>
										</li>
										<li class="list-group-item">
											<b>Business Phone Number</b>
											<p class="pull-right">{{ $account->phone }}</p>
										</li>
										<li class="list-group-item">
											<b>Business Email</b>
											<p class="pull-right">{{ $account->accountEmail }}</p>
										</li>
									</ul>
									<a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#businessAccountInfoModal">
				          	<b>Change</b>
				        	</a>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="box box-primary last-box">
								<div class="box-header with-border">
									<h3 class="box-title">Business Account Info</h3>
								</div>
								<div class="box-body">
									<ul class="list-group list-group-unbordered">
										<li class="list-group-item">
											<b>Account Type</b>
											@if($account->method == 8)
												<p class="pull-right">Checking Account</p>
											@elseif($account->method == 9)
												<p class="pull-right">Savings Account</p>
											@elseif($account->method == 10)
												<p class="pull-right">Corporate Checking Account</p>
											@elseif($account->method == 11)
												<p class="pull-right">Corporate Savings Account</p>
											@endif
										</li>
										<li class="list-group-item">
											<b>Account Number last 4</b>
											<p class="pull-right">{{ $account->accountNumber }}</p>
										</li>
										<li class="list-group-item">
											<b>Routing Number last 4</b>
											<p class="pull-right">{{ $account->routing }}</p>
										</li>
									</ul>
									<a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#sensitiveAccountInfoModal">
				          	<b>Change</b>
				        	</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
	<div class="modal fade" id="individualAccountInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header-timeline">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="individualAccountInfoModal">Edit Your Personal Info</h4>
	      </div>
	      <div class="modal-body-customer-info">
	        {!! Form::model($account, ['method' => 'PATCH', 'route' => ['accounts.personal', $account->id], 'class' => 'form-horizontal']) !!}
	          @include ('accounts.form_personal')
	        {!! Form::close() !!}
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="businessAccountInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header-timeline">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="businessAccountInfoModal">Edit Business Info</h4>
	      </div>
	      <div class="modal-body-customer-info">
	        {!! Form::model($account, ['method' => 'PATCH', 'route' => ['accounts.business', $account->id], 'class' => 'form-horizontal']) !!}
	          @include ('accounts.form_business')
	        {!! Form::close() !!}
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="sensitiveAccountInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header-timeline">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="sensitiveAccountInfoModal">Edit Business Info</h4>
	      </div>
	      <div class="modal-body-customer-info">
	        {!! Form::model($account, ['method' => 'PATCH', 'route' => ['accounts.pay', $account->id], 'class' => 'form-horizontal']) !!}
	          @include ('accounts.form_sensitive')
	        {!! Form::close() !!}
	      </div>
	    </div>
	  </div>
	</div>
</div>
@stop
@section('scripts.footer')
<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>

<script>
	intuit.ipp.anywhere.setup({
    menuProxy: '{{ env("QBO_MENU_URL") }}',
    grantUrl: '{{ env("QBO_OAUTH_URL") }}'
  });

  	import money from 'v-money'
  	Vue.use(VueMask.VueMaskPlugin);
  	Vue.use(money, {
			decimal: '.',
      thousands: ',',
      prefix: '$ ',
      precision: 2,
      masked: false
    });
    var account = new Vue({
      el: '#account',

      data: {
        ownership: {!! $account->ownership / 100 !!},
        indivState: '{!! $account->indivState !!}',
        indivZip: {!! $account->indivZip !!},
        ssn: 'XXXXX' + {!! $account->ssn !!},
        bizTaxId: '{!! $account->bizTaxId !!}',
        annualCCSales: {!! $account->annualCCSales !!},
        bizState: '{!! $account->bizState !!}',
        bizZip: {!! $account->bizZip !!},
        phone: '{!! $account->phone !!}',
        routing: 'XXXXX' + {!! $account->routing !!},
      }
    });
</script>
@stop


