@extends('layoutDashboard')

@section('content')
<div id="team">
	<div class="content-wrapper-scroll">
		<div class="scroll-main">
			<div class="scroll-main-contents">
				<section class="content-header">
			    <h1>
			      Team
			    </h1>
		    	<a href="#" data-toggle="modal" data-target="#addEmployeeModal">
		    		<button class="btn pull-right btn-primary">New Team Member</button>
		    	</a>
			    <ol class="breadcrumb">
			      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
			      <li class="active">Team</li>
			    </ol>
			  </section>
				<section class="content">
					<div class="scroll-container-analytics">
						<div class="scroll-contents">
							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
									<div class="box-success">
										<div class="box-header with-border">
											<h3 class="box-title">On Shift</h3>
											<div v-if="employeesOn != 0" class="box-tools pull-right"><span class="label label-success">@{{ employeesOn.length }} on shift</span></div>
											<div v-else class="box-tools pull-right"><span class="label label-success">0 on shift</span></div>
										</div>
										<div class="box-body no-padding">
											<ul class="users-list clearfix">
												<li v-for="employee in employeesOn">
													<img v-if="employee.photo_path" :src="employee.photo_path" alt="Employee Image">
													<img v-else src="{{ asset('/images/icon-profile-photo.png') }}" alt="User Image">
													<a class="users-list-name" href="#" v-on:click="toggleShift()">@{{ employee.first_name }} @{{ employee.last_name }}</a>
													<button class="btn btn-danger" v-on:click="toggleShift(employee.id)">Take off</button>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
									<div class="box-warning">
										<div class="box-header with-border">
											<h3 class="box-title">Off Shift</h3>
											<div v-if="employeesOff != 0" class="box-tools pull-right"><span class="label label-warning">@{{ employeesOff.length }} on shift</span></div>
											<div v-else class="box-tools pull-right"><span class="label label-warning">0 off shift</span></div>
										</div>
										<div class="box-body no-padding">
											<ul class="users-list clearfix">
												<li v-for="employee in employeesOff">
													<img v-if="employee.photo_path" :src="employee.photo_path" alt="Employee Image">
													<img v-else src="{{ asset('/images/icon-profile-photo.png') }}" alt="User Image">
													<a class="users-list-name" href="#" v-on:click="toggleShift()">@{{ employee.first_name }} @{{ employee.last_name }}</a>
													<button class="btn btn-danger" v-on:click="toggleShift(employee.id)">Put on</button>
												</li>
											</ul>
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
	<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header-timeline">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="addProductModal">Add Team Member*</h4>
	      </div>
	      <div class="modal-body-customer-info">
	        <div class="input-group">
	        	<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
	        	<input :disabled="queryFirst.length != 0 || queryLast.length != 0" v-model="queryEmail" name="queryEmail" type="email" class="form-control" placeholder="Email">
	        </div>
	        <p>- OR -</p>
	        <div class="input-group">
	        	<span class="input-group-addon"><i class="fa fa-user"></i></span>
	        	<input :disabled="queryEmail.length != 0" v-model="queryFirst" name="queryFirst" type="text" class="form-control" placeholder="First Name">
	        </div>
	        <div class="input-group">
	        	<span class="input-group-addon"><i class="fa fa-user"></i></span>
	        	<input :disabled="queryEmail.length != 0" v-model="queryLast" name="queryLast" type="text" class="form-control" placeholder="Last Name">
	        </div>
	        <button :disabled="queryFirst.length == 0 && queryLast.length == 0 && queryEmail.length == 0" class="btn btn-block btn-primary" v-on:click="searchUsers()">Search</button>
	        <table class="table" v-if="searchResult.length != 0 || searchResult != 'No Results'">
	        	<tbody>
	        		<tr v-for="person in searchResult">
	        			<td v-if="person.photo_path"><img :src="person.photo_path" alt="User Photo"></td>
	        			<td v-else><img src="{{ asset('/images/icon-profile-photo.png') }}"></td>
	        			<td>@{{ person.first_name }}</td>
	        			<td>@{{ person.last_name }}</td>
	        			<td>@{{ person.email }}</td>
	        			<td><button class="btn btn-success" v-on:click="addUser(person.id)">Add</button></td>
	        		</tr>
	        	</tbody>
	        </table>
	      </div>
	    </div>
	  </div>
	</div>
</div>
@stop

@section('scripts.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script>

	var team = new Vue({
		el: '#team',

		data: {
			employeesOn: {!! $employeesOn !!},
			employeesOff: {!! $employeesOff !!},
			queryEmail: '',
			queryFirst: '',
			queryLast: '',
			searchResult: [],

		},

		methods: {
			addUser: function(userId) {
				$.ajax({
					method: 'POST',
					url: '/employees/add',
					data: {
						'userId': userId
					},
					success: function(data) {
						console.log(data);
					}
				})
			},

			searchUsers: function() {
				if (this.queryEmail.length != 0) {
					$.ajax({
						method: 'POST',
						url: '/employees/search',
						data: {
							'email': this.queryEmail
						},
						success: function(data) {
							console.log(data);
						}
					})
				} else {
					$.ajax({
						method: 'POST',
						url: '/employees/search',
						data: {
							'firstName': this.queryFirst,
							'lastName': this.queryLast
						},
						success: function(data) {
							console.log(data);
						}
					})
				}
			},

			toggleShift: function(employeeId) {
				var businessId = '{{ $user->profile->id }}';
				$.ajax({
					method: 'POST',
					url: '/employees/toggle',
					data: {
						'businessId': businessId,
						'employeeId': employeeId
					},
					success: function(data) {
						console.log(data);
					}
				})
			}
		}
	})
</script>
@stop