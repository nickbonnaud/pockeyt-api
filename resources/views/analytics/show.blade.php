@extends('layoutDashboard')

@section('content')
<div id="content">
	<div class="content-wrapper-scroll">
		<div class="scroll-main">
			<div class="scroll-main-contents">
				<section class="content-header">
			    <h1>
			      Analytics Dashboard
			    </h1>
			    <ol class="breadcrumb">
			      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
			      <li class="active">Analytics Dashboard</li>
			    </ol>
			  </section>
				<section class="content" id="dashboard">
				
					
				</section>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script>
var tab = new Vue({
	el: '#dashboard',

	data: {
		postsInteractedWeek: {{ mostInteracted }},
		postsRevenueWeek: {{ mostRevenueGenerated }}
	},

})
	

</script>
@stop