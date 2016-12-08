@extends('layoutDashboard')

@section('content')
<div class="content-wrapper-scroll">
	<div class="scroll-main">
		<div class="scroll-main-contents">
			<section class="content-header">
		    <h1>
		      Add | Active Events
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
						<!-- @include('partials.posts.events', ['posts' => $posts, 'no_icons' => true]) -->
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

