@extends('layoutDashboard')

@section('content')
<div class="content-wrapper-scroll">
  <div class="scroll-main">
    <div class="scroll-main-contents">
    	<section class="content-header">
        <h1>
          Businesses Pending Review
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Business Review</li>
        </ol>
      </section>
    	<section class="content" id="businesses">
    		<div class="scroll-container-analytics">
    			<div class="scroll-contents">
		    		<div class="row">
			      	<div class="col-xs-12">
			      		<div class="box box-primary">
			      			<div class="box-header with-border">
			      				<h3 class="box-title-inventory">Pending Profile Approval</h3>
			      			</div>
			      			<div class="box-body table-responsive no-padding">
			      				<table class="table table-striped">
			      					<tbody>
			      						<tr>
			      							<th>Name</th>
			      							<th>Website</th>
			      							<th>Description</th>
			      							<th>Hero</th>
			      							<th>Logo</th>
			      							<th>Approve</th>
			      						</tr>
			      						@include('partials.admin.list_profiles', ['profiles' => $profiles])
			      					</tbody>
			      				</table>
			      			</div>
			      		</div>
			      	</div>
			      </div>
			      <div class="row">
			      	<div class="col-xs-12">
			      		<div class="box box-primary">
			      			<div class="box-header with-border">
			      				<h3 class="box-title-inventory">Pending Account Approval</h3>
			      			</div>
			      			<div class="box-body table-responsive no-padding">
			      				<table class="table table-striped">
			      					<tbody>
			      						<tr>
			      							<th>Name</th>
			      							<th>Business Info</th>
			      							<th>Owner Info</th>
			      							<th>Bank Info</th>
			      							<th>Description</th>
			      							<th>Approve</th>
			      						</tr>
			      						@include('partials.admin.list_accounts', ['accounts' => $accounts])
			      					</tbody>
			      				</table>
			      			</div>
			      		</div>
			      	</div>
			      </div>
			  	</div>
			  </div>
    	</section>
    </div>
  </div>
</div>
<div class="modal fade" id="mccModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header-timeline">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="mccModal">Select Business MCC</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <p>@{{ selectAccount.id }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
<script>
	var businesses = new Vue({
		el: '#businesses',

		data: {
			selectAccount: {}
		}
	})
</script>