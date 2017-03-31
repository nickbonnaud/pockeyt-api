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
							<div class="nav-tabs-custom">
								<ul class="nav nav-tabs pull-right">
									<li class="active"><a href="#week-chart" data-toggle="tab">7 Days</a></li>
									<li><a href="#month-chart" data-toggle="tab" v-on:click="MonthInteractionData()">30 Days</a></li>
									<li><a href="#2month-chart" data-toggle="tab">60 Days</a></li>
									<li class="pull-left header"><i class="fa fa-hand-o-up"></i> Interactions</li>
								</ul>
								<div class="tab-content no-padding">
									<div class="chart tab-pane active" id="week-chart">
										<canvas id="barChartInter" width="400" height="400"></canvas>
									</div>
									<div class="chart tab-pane active" id="month-chart">
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
			postsInteractedMonth: [],
			postsRevenueWeek: {!! $mostRevenueGenerated !!},
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
							backgroundColor: "rgba(52, 152, 219,.8)",
							hoverBorderColor: "rgba(41, 128, 185,1.0)",
          		data: data
						}
					]
				}
				return barChartData;
			},
			MonthInteractionData: function() {
				var businessId = '{{ $profile->id }}';
				console.log(businessId);
				var timeSpan = "month";
				var type = "interaction";
				this.getData(businessId, timeSpan, type);
			},
			getData: function(businessId, timeSpan, type) {
				$.ajax({
					method: 'POST',
					url: '/analytics/dashboard/data',
					data: {
						'businessId': businessId,
						'timeSpan': timeSpan,
						'type': type
					},
					success: data => {
						console.log(data);
					},
					error: data => {
						console.log(data);
					}
				})
			}
		}

	})
	

</script>
@stop