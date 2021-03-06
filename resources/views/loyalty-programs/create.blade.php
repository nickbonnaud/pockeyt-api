@extends('layoutDashboard')

@section('content')
<div class="content-wrapper-scroll" id="loyalty">
	<div class="scroll-main">
		<div class="scroll-main-contents">
			<section class="content-header">
		    <h1>
		      Create your Customer Loyalty Program
		    </h1>
		    <ol class="breadcrumb">
		      <li><a href="{{ route('profiles.show', ['profiles' => Crypt::encrypt($user->profile->id)]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
		      <li class="active">Loyalty Program</li>
		    </ol>
		  </section>
		  @include ('errors.form')
			<section class="content">
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title">Program Details</h3>
						</div>
						{!! Form::open(['route' => 'loyalty-programs.store']) !!}
							@include('partials.loyalty-programs.form_create')
						{!! Form::close() !!}
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
@stop
@section('scripts.footer')
<script>
	
	var loyalty = new Vue({
		el: '#loyalty',

		components: {
    	VMoney
    },
		
		data: {
			selection: "",
			amount_required: "",
			money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false
      }
		}
	});

</script>
@stop




