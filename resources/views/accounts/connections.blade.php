@extends('layoutDashboard')
@section('content')
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
											<th>Status</th>
											<th>Toggle</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><span class="icon-fb-connect"></span></td>
											<td>Auto Posting</td>
											@if($user->profile->fb_page_id !== null)
												<td><span class="label label-success">Enabled</span></td>
												<td><span class="label label-danger">Disable</span></td>
											@else
												<td><span class="label label-warning">Disabled</span></td>
												<td><span class="label label-info">Enable</span></td>
											@endif
										</tr>
										<tr>
											<td><span class="icon-insta-connect"></span></td>
											<td>Auto Posting</td>
											@if(($user->profile->fb_page_id == null) && ($user->profile->connected == true))
												<td><span class="label label-success">Enabled</span></td>
												<td><span class="label label-danger">Disable</span></td>
											@else
												<td><span class="label label-warning">Disabled</span></td>
												<td><span class="label label-info">Enable</span></td>
											@endif
										</tr>
										<tr>
											<td><span class="icon-square-connect"></span></td>
											<td>Inventory Import</td>
											@if(isset($user->profile->square_token))
												<td><span class="label label-success">Enabled</span></td>
												<td><span class="label label-danger">Disable</span></td>
											@else
												<td><span class="label label-warning">Disabled</span></td>
												<td><span class="label label-info">Enable</span></td>
											@endif
										</tr>
										<tr>
											<td><span class="icon-square-connect"></span></td>
											<td>Pockeyt Lite</td>
											@if($user->profile->account->pockeyt_lite_enabled)
												<td><span class="label label-success">Enabled</span></td>
												<td><span class="label label-danger">Disable</span></td>
											@else
												<td><span class="label label-warning">Disabled</span></td>
												<td><span class="label label-info">Enable</span></td>
											@endif
										</tr>
										<tr>
											<td><span class="icon-quickbooks-connect"></span></td>
											<td>Pockeyt Sync</td>
											@if($user->profile->connected_qb)
												<td><span class="label label-success">Enabled</span></td>
												<td><span class="label label-danger">Disable</span></td>
											@else
												<td><span class="label label-warning">Disabled</span></td>
												<td><span class="label label-info">Enable</span></td>
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



