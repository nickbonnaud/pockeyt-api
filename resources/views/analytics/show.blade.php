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
					<div class="row">
						<div class="col-md-6">
							<div class="box box-primary">
								<div class="box-header with-border">
									<h3 class="box-title">Top 10 Most Interacted with Posts</h3>
									<div class="box-tools pull-right">
										<button type="button" class="btn btn-box-tool" data-widget="collapse">
											<i class="fa fa-minus"></i>
										</button>
										<button type="button" class="btn btn-box-tool" data-widget="remove">
											<i class="fa fa-times"></i>
										</button>
									</div>
								</div>
								<div class="box-body">
									<div class="chart">
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script>

	var thing = postsInteractedWeek;
	console.log(thing);

	var barChartCanvas = $("#barchart").get(0).getContext("2d");
	var barChart = new Chart(barChartCanvas);
	var barChartData = areaCharData;

	var tab = new Vue({
		el: '#dashboard',

		data: {
			postsInteractedWeek: {!! $mostInteracted !!},
			postsRevenueWeek: {!! $mostRevenueGenerated !!}
		},

	})
	

</script>
@stop