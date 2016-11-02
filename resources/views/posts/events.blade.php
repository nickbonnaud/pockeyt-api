@extends('layoutDashboard')

@section('content')
<div class="content-wrapper">
	<section class="content-header">
    <h1>
      Add | Recent Events
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Events</li>
    </ol>
  </section>
	<section class="content">
	<div class="col-md-6">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Create a New Event</h3>
			</div>
				{!! Form::open(['method' => 'POST', 'route' => ['posts.store'], 'files' => true]) !!}
				@include('posts.event_form')
				{!! Form::close() !!}
		</div>
	</div>
		@include('partials.posts.events', ['posts' => $posts, 'no_icons' => true])
	</section>
</div>

@stop

@section('scripts.footer')
	<script>
		$(function() {
      $( "#event_date_pretty" ).datepicker({
          dateFormat: "DD, d MM, yy",
          altField: "#event_date",
          altFormat: "yy-mm-dd"
      });
    });
	</script>

@stop

