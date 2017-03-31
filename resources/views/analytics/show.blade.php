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
										<canvas id="barChartInter" width="400" height="400"></canvas>
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

	// var barChartCanvas = $("#barchart").get(0).getContext("2d");
	// var barChart = new Chart(barChartCanvas);
	// var barChartData = areaCharData;

	var tab = new Vue({
		el: '#dashboard',

		data: {
			postsInteractedWeek: {!! $mostInteracted !!},
			postsRevenueWeek: {!! $mostRevenueGenerated !!}
		},

		mounted: function() {
			var barChartCanvas = $("#barChartInter").get(0).getContext("2d");
			var barChartData = this.formatBarData(this.postsInteractedWeek);
			var barChartOptions = {
	      scaleShowGridLines: true,
	      scaleGridLineColor: "rgba(0,0,0,.05)",
	      scaleGridLineWidth: 1,
	      scaleShowHorizontalLines: true,
	      scaleShowVerticalLines: false,
	      barShowStroke: true,
	      barStrokeWidth: 2,
	      barValueSpacing: 5,
	      barDatasetSpacing: 1,
	      responsive: true,
	      maintainAspectRatio: true,
	      scales: {
	      	yAxes: [{
	      		ticks: {
	      			beginAtZero: true
	      		}
	      	}]
	      }
			};
    	var barChart = new Chart(barChartCanvas, {
    		type: 'bar',
    		data: barChartData,
    		options: barChartOptions
    	});
		},

		methods: {
			formatBarData: function(dataSet) {
				var dataSetTrimmed = dataSet.slice(0,9);
				var labels = [];
				var data = [];
				
				dataSetTrimmed.forEach(function(post) {
					var postLabel = post.message;
					if (!postLabel) {
						postLabel = post.title;
					}
					if (postLabel.length > 10) postLabel = postLabel.substring(0, 10) + "...";
					labels.push(postLabel);

					var interactions = post.total_interactions;
					data.push(interactions);
				});
				var barChartData = {
					labels: labels,
					datasets: [
						{
							label: "Views, Shares, Bookmarks",
							backgroundColor: "rgba(52, 152, 219,.5)",
							hoverBorderColor: "rgba(41, 128, 185,1.0)",
          		data: data
						}
					]
				}
				return barChartData;
			}
		}

	})
	

</script>
@stop