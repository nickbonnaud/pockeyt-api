@extends('layoutDashboard')

@section('content')
<div class="content-wrapper-scroll">
	<div class="scroll-main">
		<div class="scroll-main-contents">
			<section class="content-header">
		    <h1>
		      Your Customer Loyalty Program
		    </h1>
		    <ol class="breadcrumb">
		      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
		      <li class="active">Loyalty Program</li>
		    </ol>
		  </section>
		  @include ('errors.form')
			<section class="content" id="content">
				<div class="col-md-6">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title">Program Details</h3>
						</div>
						<div class="box-body">
							@if($loyaltyProgram->is_increment)
								<h4>Your current Loyalty Program requires {{ $loyaltyProgram->purchases_required }} purchases per reward.</h4>
							@else
								<h4>Your current Loyalty Program requires ${{ $loyaltyProgram->amount_required }}'s in total purchases per reward.</h4>
							@endif
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
@stop
@section('scripts.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script>
	
	var content = new Vue({
		el: '#content',

		data: {
			selection: ""
		}
	})

</script>
@stop