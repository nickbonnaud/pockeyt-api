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
							<a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#LoyaltyProgramModal">
		          	<b>Change</b>
		        	</a>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
<div class="modal fade" id="LoyaltyProgramModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="LoyaltyProgramModal">Change Loyalty Program</h4>
      </div>
      <div class="modal-body">
        {!! Form::model($loyaltyProgram, ['method' => 'PATCH', 'route' => ['loyaltyProgram.update', $loyaltyProgram->id]]) !!}
          @include ('partials.loyalty-programs.form_edit')
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@stop

@section('scripts.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script>
	
	var selection = {
		fetch: function() {
			if ({{ $loyaltyProgram->is_increment }}) {
				var selected = "increments";
			} else {
				var selected = "amounts";
			}
			return selected;
		}
	}

	var content = new Vue({
		el: '#content',

		data: {
			selection: selection.fetch()
		}
	})

</script>
@stop