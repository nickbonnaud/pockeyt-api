@extends('layoutDashboard')

@section('content')
<div class="content-wrapper-scroll">
	<div class="scroll-main">
		<div class="scroll-main-contents">
			<section class="content-header">
		    <h1>
		      Add | Recent Events
		    </h1>
		    @if($user->profile->connected == false)
          <a href="#" data-toggle="modal" data-target="#connectSocial">
          	<h4>Enable Auto Posting</h4>
          </a>
        @endif
		    <ol class="breadcrumb">
		      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
		      <li class="active">Events</li>
		    </ol>
		  </section>
			<section class="content">
			<div class="col-md-6">
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Create a New Event</h3>
					</div>
						@include ('errors.form')
						{!! Form::open(['method' => 'POST', 'route' => ['events.store'], 'files' => true]) !!}
						@include('posts.event_form')
						{!! Form::close() !!}
				</div>
			</div>
				<div class="scroll-container col-md-6">
					<div class="scroll-contents">
						@include('partials.posts.events', ['posts' => $posts, 'no_icons' => true])
					</div>
				</div>
			</section>
		</div>
	</div>
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

