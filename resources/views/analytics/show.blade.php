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
									<li class="active"><a href="#week-inter-chart" data-toggle="tab" v-on:click="weekInteractionData()">7 Days</a></li>
									<li><a href="#month-inter-chart" data-toggle="tab" v-on:click="monthInteractionData()">30 Days</a></li>
									<li><a href="#2month-inter-chart" data-toggle="tab" v-on:click="twoMonthInteractionData()">60 Days</a></li>
									<li class="pull-left header"><i class="fa fa-hand-o-up"></i> Top 10 Posts by Interactions</li>
								</ul>
								<div class="tab-content no-padding">
									<div class="chart tab-pane active" id="week-inter-chart">
										<canvas id="barInteractionsWeek" width="400" height="400"></canvas>
									</div>
									<div class="chart tab-pane" id="month-inter-chart">
										<canvas id="barInteractionsMonth" width="400" height="400"></canvas>
									</div>
									<div class="chart tab-pane" id="2month-inter-chart">
										<canvas id="barInteractions2Month" width="400" height="400"></canvas>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="nav-tabs-custom">
								<ul class="nav nav-tabs pull-right">
									<li class="active"><a href="#week-revenue-chart" data-toggle="tab" v-on:click="weekRevenueData()">7 Days</a></li>
									<li><a href="#month-revenue-chart" data-toggle="tab" v-on:click="monthRevenueData()">30 Days</a></li>
									<li><a href="#2month-revenue-chart" data-toggle="tab" v-on:click="twoMonthRevenueData()">60 Days</a></li>
									<li class="pull-left header"><i class="fa fa-money"></i> Top 10 Posts by Revenue</li>
								</ul>
								<div class="tab-content no-padding">
									<div class="chart tab-pane active" id="week-revenue-chart">
										<canvas id="barRevenueWeek" width="400" height="400"></canvas>
									</div>
									<div class="chart tab-pane" id="month-revenue-chart">
										<canvas id="barRevenueMonth" width="400" height="400"></canvas>
									</div>
									<div class="chart tab-pane" id="2month-revenue-chart">
										<canvas id="barRevenue2Month" width="400" height="400"></canvas>
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


	var tab = new Vue({
		el: '#dashboard',

		data: {
			postsInteractedWeek: {!! $mostInteracted !!},
			postsInteractedMonth: [],
			postsInteracted2Month: [],

			postsRevenueWeek: {!! $mostRevenueGenerated !!},
			postsRevenueMonth: [],
			postsRevenue2Month: []
		},

		mounted: function() {
			var barInteractionsWeek = $("#barInteractionsWeek").get(0).getContext("2d");
			var type = "interaction";
			var barInteractionsWeekData = this.formatBarData(this.postsInteractedWeek, type);
			
    	var barChartInter7 = new Chart(barInteractionsWeek, {
    		type: 'bar',
    		data: barInteractionsWeekData,
    		options: barChartOptions
    	});

    	var barRevenueWeek = $("#barInteractionsWeek").get(0).getContext("2d");
    	var type = "revenue";
			var barRevenueWeekData = this.formatBarData(this.postsInteractedWeek, type);
			
    	var barChartRevenue7 = new Chart(barRevenueWeek, {
    		type: 'bar',
    		data: barRevenueWeekData,
    		options: barChartOptions
    	});
		},

		methods: {
			formatBarData: function(dataSet, type) {
				var dataSetTrimmed = dataSet.slice(0,10);
				var labels = [];
				var data = [];
				
				dataSetTrimmed.forEach(function(post) {
					var postLabel = post.message;
					if (!postLabel) {
						postLabel = post.title;
					}
					if (postLabel.length > 10) postLabel = postLabel.substring(0, 10) + "...";
					labels.push(postLabel);

					if (type === "interaction") {
						var interactions = post.total_interactions;
						data.push(interactions);
					} else {
						var revenue = post.total_revenue;
						data.push(revenue);
					}
				});
				if (type === "interaction") {
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
				} else {
					var barChartData = {
						labels: labels,
						datasets: [
							{
								label: "Revenue Per Post",
								backgroundColor: "rgba(46, 204, 113,.8)",
								hoverBorderColor: "rgba(39, 174, 96,1.0)",
	          		data: data
							}
						]
					}
				}
				return barChartData;
			},
			weekInteractionData: function() {
				var businessId = '{{ $user->profile->id }}';
				var timeSpan = "week";
				var type = "interaction";
				this.getData(businessId, timeSpan, type);
			},
			monthInteractionData: function() {
				var businessId = '{{ $user->profile->id }}';
				var timeSpan = "month";
				var type = "interaction";
				this.getData(businessId, timeSpan, type);
			},
			twoMonthInteractionData: function() {
				var businessId = '{{ $user->profile->id }}';
				var timeSpan = "2month";
				var type = "interaction";
				this.getData(businessId, timeSpan, type);
			},
			weekRevenueData: function() {
				var businessId = '{{ $user->profile->id }}';
				var timeSpan = "week";
				var type = "revenue";
				this.getData(businessId, timeSpan, type);
			},
			monthRevenueData: function() {
				var businessId = '{{ $user->profile->id }}';
				var timeSpan = "month";
				var type = "revenue";
				this.getData(businessId, timeSpan, type);
			},
			twoMonthRevenueData: function() {
				var businessId = '{{ $user->profile->id }}';
				var timeSpan = "2month";
				var type = "revenue";
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
						var timeSpan = data.timeSpan;
						var type = data.type;
						var dataSet = data.data;

						switch(timeSpan) {
							case "week":
								if (type === 'interaction') {
									this.postsInteractedWeek = dataSet;
									var barInteractionsWeek = $("#barInteractionsWeek").get(0).getContext("2d");
									var barInteractionsWeekData = this.formatBarData(this.postsInteractedWeek, type);
									
						    	var barChartInter7 = new Chart(barInteractionsWeek, {
						    		type: 'bar',
						    		data: barInteractionsWeekData,
						    		options: barChartOptions
						    	});
								} else {
									this.postsRevenueWeek = dataSet;
									var barRevenueWeek = $("#barRevenueWeek").get(0).getContext("2d");
									var barRevenueWeekData = this.formatBarData(this.postsRevenueWeek, type);
									
						    	var barChartRevenue7 = new Chart(barRevenueWeek, {
						    		type: 'bar',
						    		data: barRevenueWeekData,
						    		options: barChartOptions
						    	});
								}
								break;
							case "month":
								if (type === 'interaction') {
									this.postsInteractedMonth = dataSet;
									var barInteractionsMonth = $("#barInteractionsMonth").get(0).getContext("2d");
									var barInteractionsMonthData = this.formatBarData(this.postsInteractedMonth, type);
									var barChartInter30 = new Chart(barInteractionsMonth, {
						    		type: 'bar',
						    		data: barInteractionsMonthData,
						    		options: barChartOptions
						    	});
								} else {
									this.postsRevenueMonth = dataSet;
									var barRevenueMonth = $("#barRevenueMonth").get(0).getContext("2d");
									var barRevenueMonthData = this.formatBarData(this.postsRevenueMonth, type);
									
						    	var barChartRevenue30 = new Chart(barRevenueMonth, {
						    		type: 'bar',
						    		data: barRevenueMonthData,
						    		options: barChartOptions
						    	});
								}
								break;
							case "2month":
								if (type === 'interaction') {
									this.postsInteracted2Month = dataSet;
									var barInteractions2Month = $("#barInteractions2Month").get(0).getContext("2d");
									var barInteractions2MonthData = this.formatBarData(this.postsInteracted2Month, type);
									var barChartInter60 = new Chart(barInteractions2Month, {
						    		type: 'bar',
						    		data: barInteractions2MonthData,
						    		options: barChartOptions
						    	});
								} else {
									this.postsRevenue2Month = dataSet;
									var barRevenue2Month = $("#barRevenue2Month").get(0).getContext("2d");
									var barRevenue2MonthData = this.formatBarData(this.postsRevenue2Month, type);
									
						    	var barChartRevenue60 = new Chart(barRevenue2Month, {
						    		type: 'bar',
						    		data: barRevenue2MonthData,
						    		options: barChartOptions
						    	});
								}
						}
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