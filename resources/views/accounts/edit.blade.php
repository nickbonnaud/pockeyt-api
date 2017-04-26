@extends('layoutDashboard')
@section('content')
<?php
$qbo_obj = new \App\Http\Controllers\QuickBookController();
$qbo_connect = $qbo_obj->qboConnect();
?>
<div class="content-wrapper-scroll">
	<div class="scroll-main">
		<div class="scroll-main-contents">
			<section class="content-header">
		    <h1>
		      Your Business Account Profile
		    </h1>
		     @if(!$qbo_connect)
		    	<span class="pull-right">
						<ipp:connectToIntuit></ipp:connectToIntuit>
					</span>
				@endif
		    @if($account->status == 'pending')
		    	<p><i class="fa fa-circle text-warning"></i> Account Pending</p>
		    @elseif($account->status == 'active')
		    	<p><i class="fa fa-circle text-success"></i> Account Active</p>
		    @else
		    	<p><i class="fa fa-circle text-danger"></i> Account Not Approved</p>
		    	<p>{{ $account->status }}</p>
		    @endif
		    <ol class="breadcrumb">
		      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
		      <li class="active">Payment Account Info</li>
		    </ol>
		  </section>
		  @include ('errors.form')
			<section class="content">
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
									<p class="pull-right">{{ $account->accountEmail }}</p>
								</li>
								<li class="list-group-item">
									<b>Date of Birth</b>
									<p class="pull-right">{{ $account->dateOfBirth }}</p>
								</li>
								<li class="list-group-item">
									<b>Last Four SSN</b>
									<p class="pull-right">{{ $account->last4 }}</p>
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
									<b>Business Tax ID</b>
									<p class="pull-right">{{ $account->bizTaxId }}</p>
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
									<b>Account Number</b>
									<p class="pull-right">{{ $account->accountNumber4 }}</p>
								</li>
								<li class="list-group-item">
									<b>Routing Number</b>
									<p class="pull-right">{{ $account->routingNumber4 }}</p>
								</li>
							</ul>
							<a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#sensitiveAccountInfoModal">
		          	<b>Change</b>
		        	</a>
						</div>
					</div>
				</div>
			</section>
		</div>
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
@stop
@section('scripts.footer')
<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>

<script>
	intuit.ipp.anywhere.setup({
    menuProxy: '{{ env("QBO_MENU_URL") }}',
    grantUrl: '{{ env("QBO_OAUTH_URL") }}'
  });
</script>



