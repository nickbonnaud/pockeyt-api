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
		      Your Account Connections
		    </h1>
		    <ol class="breadcrumb">
		      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
		      <li class="active">Account Connections Info</li>
		    </ol>
		  </section>
		  @include ('errors.form')
			<section class="content">
				<div class="col-md-12">
					<div class="box box-info">
						<div class="box-header with-border">
							<h3 class="box-title">Connections and Status</h3>
						</div>
						<div class="box-body">
							<div class="table-responsive">
								<table class="table no-margin">
									<thead>
										<tr>
											<th>Company</th>
											<th>Service</th>
											<th>Change Connection</th>
											<th>Feature</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><span class="icon-fb-connect"></span></td>
											<td>Auto Posting</td>
											@if($user->profile->fb_app_id !== null)
												<td><span class="label label-success">Connected</span></td>
												@if($user->profile->fb_page_id !== null && $user->profile->connected == 'facebook')
													<td>
														<a href="{{ action('ConnectController@removefBSubscription') }}">
															<button class="btn btn-danger">Disable</button>
														</a>
													</td>
												@else
													<td>
														<a href="{{ action('ConnectController@addfBSubscription') }}">
															<button class="btn btn-success">Enable</button>
														</a>
													</td>
												@endif
											@else
												<td>
													<a href="{{ action('ConnectController@connectFB') }}" class="btn btn-block btn-social btn-facebook">
											      <i class="fa fa-facebook"></i>
											      Connect With Facebook
										  		</a>
										  	</td>
												<td><button class="btn btn-success disabled">Enable</button></td>
											@endif
										</tr>
										<tr>
											<td><span class="icon-insta-connect"></span></td>
											<td>Auto Posting</td>
											@if($user->profile->insta_account_id !== null)
												<td><span class="label label-success">Connected</span></td>
												@if($user->profile->insta_account_token !== null)
													<td>
														<a href="{{ action('ConnectController@removeInstaSubscription') }}">
															<button class="btn btn-danger">Disable</button>
														</a>
													</td>
												@else
													<td>
														<a href="{{ action('ConnectController@connectInsta') }}">
															<button class="btn btn-success">Enable</button>
														</a>
													</td>
												@endif
											@else
												<td>
													<a href="{{ action('ConnectController@connectInsta') }}" class="btn btn-block btn-social btn-instagram">
	      										<i class="fa fa-instagram"></i>
	      										Connect With Instagram
  												</a>
  											</td>
												<td><button class="btn btn-success disabled">Enable</button></td>
											@endif
										</tr>
										<tr>
											<td><span class="icon-square-connect"></span></td>
											<td>Inventory Import</td>
											@if(isset($user->profile->square_token))
												<td><button class="btn btn-danger">Disconnect</button></td>
												<td><span class="label label-primary">Disable by disconnecting</span></td>
											@else
												<td>
													<a href="{{ 'https://connect.squareup.com/oauth2/authorize?client_id=' . env('SQUARE_ID') . '&scope=ITEMS_READ%20ITEMS_WRITE%20MERCHANT_PROFILE_READ%20PAYMENTS_READ&state=' . env('SQUARE_STATE') }}" class="btn btn-block btn-social btn-github">
														<i class="fa fa-square-o"></i>
														Connect With Square
													</a>
												</td>
												<td><span class="label label-primary">Enable by connecting</span></td>
											@endif
										</tr>
										<tr>
											<td><span class="icon-square-connect"></span></td>
											<td>Pockeyt Lite</td>
											@if(isset($user->profile->square_token))
												<td><button class="btn btn-danger">Disconnect</button></td>
												@if($user->profile->account->pockeyt_lite_enabled)
													<td><button class="btn btn-danger">Disable</button></td>
												@else
													<td><a href="{{ action('ConnectController@subscribeSquare') }}"><button class="btn btn-success">Enable</button></a></td>
												@endif
											@else
												<td>
													<a href="{{ 'https://connect.squareup.com/oauth2/authorize?client_id=' . env('SQUARE_ID') . '&scope=ITEMS_READ%20ITEMS_WRITE%20MERCHANT_PROFILE_READ%20PAYMENTS_READ&state=' . env('SQUARE_STATE') }}" class="btn btn-block btn-social btn-github">
														<i class="fa fa-square-o"></i>
														Connect With Square
													</a>
												</td>
												<td><button class="btn btn-success disabled">Enable</button></td>
											@endif
										</tr>
										<tr>
											<td><span class="icon-quickbooks-connect"></span></td>
											<td>Pockeyt Sync</td>
											@if($user->profile->connected_qb)
												<td><button class="btn btn-danger" href="{{ action('QuickBookController@qboDisconnect') }}">Disconnect</button></td>
												<td><span class="label label-primary">Disable by disconnecting</span></td>
											@else
												<td style="text-align: center;"><ipp:connectToIntuit></ipp:connectToIntuit></td>
												<td><span class="label label-primary">Enable by connecting</span></td>
											@endif
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</section>
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


