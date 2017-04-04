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
						<div class="scroll-container-analytics">
							<div class="scroll-contents">
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
												<canvas id="barInteractionsWeek" width="400" height="300"></canvas>
											</div>
											<div class="chart tab-pane" id="month-inter-chart">
												<canvas id="barInteractionsMonth" width="400" height="300"></canvas>
											</div>
											<div class="chart tab-pane" id="2month-inter-chart">
												<canvas id="barInteractions2Month" width="400" height="300"></canvas>
											</div>
										</div>
									</div>
									<div class="nav-tabs-custom">
										<ul class="nav nav-tabs pull-right">
											<li class="active"><a href="#day-inter-chart" data-toggle="tab" v-on:click="dayInterData()">Interactions</a></li>
											<li><a href="#day-revenue-chart" data-toggle="tab" v-on:click="dayRevenueData()">Revenue</a></li>
											<li class="pull-left header"><i class="fa fa-calendar-o"></i> Percentage Activity by Day</li>
										</ul>
										<div class="tab-content">
											<div class="chart tab-pane active" id="day-inter-chart">
												<canvas id="lineInterDay" width="400" height="300"></canvas>
											</div>
											<div class="chart tab-pane" id="day-revenue-chart">
												<canvas id="lineRevenueDay" width="400" height="300"></canvas>
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
												<canvas id="barRevenueWeek" width="400" height="300"></canvas>
											</div>
											<div class="chart tab-pane" id="month-revenue-chart">
												<canvas id="barRevenueMonth" width="400" height="300"></canvas>
											</div>
											<div class="chart tab-pane" id="2month-revenue-chart">
												<canvas id="barRevenue2Month" width="400" height="300"></canvas>
											</div>
										</div>
									</div>
									<div class="nav-tabs-custom">
										<ul class="nav nav-tabs pull-right">
											<li class="active"><a href="#hour-inter-chart" data-toggle="tab" v-on:click="hourInterData()">Interactions</a></li>
											<li><a href="#hour-revenue-chart" data-toggle="tab" v-on:click="hourRevenueData()">Revenue</a></li>
											<li class="pull-left header"><i class="fa fa-hourglass-2"></i> Percentage Activity by Time</li>
										</ul>
										<div class="tab-content">
											<div class="chart tab-pane active" id="hour-inter-chart">
												<canvas id="lineInterHour" width="400" height="300"></canvas>
											</div>
											<div class="chart tab-pane" id="hour-revenue-chart">
												<canvas id="lineRevenueHour" width="400" height="300"></canvas>
											</div>
										</div>
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
<div class="modal fade" id="showPost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" v-for="post in selectedpost">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="showPostModal">@{{post.message}}</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
         <p>Hello World</p>
        </div>
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

	var barChartOptionsRevenue = {
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
    			beginAtZero: true,
    			callback: function(value, index, values) {
            if(parseInt(value) >= 1000){
              return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            } else {
              return '$' + value;
            }
          } 
    		}
    	}]
    }
	};

	var lineChartOptions = {
		responsive: true,
    maintainAspectRatio: true,
		scales: {
			yAxes: [{
    		ticks: {
    			beginAtZero: true,
    			callback: function(value, index, values) {
            return value + '%';
          } 
    		}
    	}],
      xAxes: [{
        ticks: {
          autoSkip: true,
          autoSkipPadding: 5
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
			postsRevenue2Month: [],

			postsActivityByDay: {!! $activityByDay !!},
			postsRevenueByDay: [],

			postsActivityByHour: {!! $activityByHour !!},
			postsRevenueByHour: [],

			selectedpost:[]
		},

		mounted: function() {
			var barInteractionsWeekRaw = $("#barInteractionsWeek").get(0);
			var barInteractionsWeek = barInteractionsWeekRaw.getContext("2d");
			var type = "interaction";
			var barInteractionsWeekData = this.formatBarData(this.postsInteractedWeek, type);
    	var barChartInter7 = new Chart(barInteractionsWeek, {
    		type: 'bar',
    		data: barInteractionsWeekData,
    		options: barChartOptions
    	});

    	var data = this.postsInteractedWeek;
    	console.log(data);
    	barInteractionsWeekRaw.onclick = function(evt, data) {
    		var activePoints = barChartInter7.getElementsAtEvent(evt);
    		var idx = activePoints[0]['_index'];
    		var post = data[idx];
    		this.selectedpost = [];
    		this.selectedpost.push(post);
    		$('#showPost').modal('show');
    	};

    	var barRevenueWeek = $("#barRevenueWeek").get(0).getContext("2d");
    	var type = "revenue";
			var barRevenueWeekData = this.formatBarData(this.postsRevenueWeek, type);
    	var barChartRevenue7 = new Chart(barRevenueWeek, {
    		type: 'bar',
    		data: barRevenueWeekData,
    		options: barChartOptionsRevenue
    	});

    	var lineInteractionsDay = $("#lineInterDay").get(0).getContext("2d");
    	var type = "interaction";
    	var lineInteractionsDayData = this.formatLineData(this.postsActivityByDay, type);
    	var lineChartInter = new Chart(lineInteractionsDay, {
    		type: 'line',
    		data: lineInteractionsDayData,
    		options: lineChartOptions
    	});

    	var lineInteractionsHour = $("#lineInterHour").get(0).getContext("2d");
    	var type = "interaction";
    	var lineInteractionsHourData = this.formatLineDataHour(this.postsActivityByHour, type);
    	var lineChartInterHour = new Chart(lineInteractionsHour, {
    		type: 'line',
    		data: lineInteractionsHourData,
    		options: lineChartOptions
    	});
		},

		methods: {
			formatLineDataHour: function(dataSet, type) {
				var data = dataSet;
				var labels = ['12am', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12pm', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'];
				if (type === 'interaction') {
					var lineChartData = {
						labels: labels,
						datasets: [
							{
								label: "% Views, Shares, Bookmarks",
								fill: false,
								backgroundColor: "rgba(52, 152, 219,0.4)",
								borderColor: "rgba(52, 152, 219,1.0)",
								borderCapStyle: "round",
								borderDash: [],
								borderDashOffset: 0.0,
								borderJoinStyle: 'bevel',
								pointBorderColor: "rgba(52, 152, 219,1.0)",
								pointBackgroundColor: "#fff",
								pointBorderWidth: 1,
            		pointHoverRadius: 5,
            		pointHoverBackgroundColor: "rgba(41, 128, 185,1.0)",
            		pointHoverBorderColor: "rgba(41, 128, 185,1.0)",
            		pointHoverBorderWidth: 2,
            		pointRadius: 1,
	          		data: data,
	          		spanGaps: false,
							}
						]
					}
				} else {
					var lineChartData = {
						labels: labels,
						datasets: [
							{
								label: "% Revenue",
								fill: false,
								backgroundColor: "rgba(46, 204, 113,.4)",
								borderColor: "rgba(46, 204, 113,1.0)",
								borderCapStyle: "round",
								borderDash: [],
								borderDashOffset: 0.0,
								borderJoinStyle: 'bevel',
								pointBorderColor: "rgba(46, 204, 113,1.0)",
								pointBackgroundColor: "#fff",
								pointBorderWidth: 1,
            		pointHoverRadius: 5,
            		pointHoverBackgroundColor: "rgba(39, 174, 96,1.0)",
            		pointHoverBorderColor: "rgba(39, 174, 96,1.0)",
            		pointHoverBorderWidth: 2,
            		pointRadius: 1,
	          		data: data,
	          		spanGaps: false,
							}
						]
					}
				}
				return lineChartData;
			},
			formatLineData: function(dataSet, type) {
				console.log(dataSet);
				var data = dataSet;
				var labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

				if (type === 'interaction') {
					var lineChartData = {
						labels: labels,
						datasets: [
							{
								label: "AVG Views, Shares, Bookmarks",
								fill: false,
								backgroundColor: "rgba(52, 152, 219,0.4)",
								borderColor: "rgba(52, 152, 219,1.0)",
								borderCapStyle: "round",
								borderDash: [],
								borderDashOffset: 0.0,
								borderJoinStyle: 'bevel',
								pointBorderColor: "rgba(52, 152, 219,1.0)",
								pointBackgroundColor: "#fff",
								pointBorderWidth: 1,
            		pointHoverRadius: 5,
            		pointHoverBackgroundColor: "rgba(41, 128, 185,1.0)",
            		pointHoverBorderColor: "rgba(41, 128, 185,1.0)",
            		pointHoverBorderWidth: 2,
            		pointRadius: 1,
	          		data: data,
	          		spanGaps: false,
							}
						]
					}
				} else {
					var lineChartData = {
						labels: labels,
						datasets: [
							{
								label: "AVG Revenue",
								fill: false,
								backgroundColor: "rgba(46, 204, 113,.4)",
								borderColor: "rgba(46, 204, 113,1.0)",
								borderCapStyle: "round",
								borderDash: [],
								borderDashOffset: 0.0,
								borderJoinStyle: 'bevel',
								pointBorderColor: "rgba(46, 204, 113,1.0)",
								pointBackgroundColor: "#fff",
								pointBorderWidth: 1,
            		pointHoverRadius: 5,
            		pointHoverBackgroundColor: "rgba(39, 174, 96,1.0)",
            		pointHoverBorderColor: "rgba(39, 174, 96,1.0)",
            		pointHoverBorderWidth: 2,
            		pointRadius: 1,
	          		data: data,
	          		spanGaps: false,
							}
						]
					}
				}
				return lineChartData;
			},
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
						var revenue = post.total_revenue / 100;
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
			hourInterData: function() {
				var businessId = '{{ $user->profile->id }}';
				var type = "interaction";
				this.getDataLineHour(businessId, type);
			},
			hourRevenueData: function() {
				var businessId = '{{ $user->profile->id }}';
				var type = "revenue";
				this.getDataLineHour(businessId, type);
			},
			dayInterData: function() {
				var businessId = '{{ $user->profile->id }}';
				var type = "interaction";
				this.getDataLine(businessId, type);
			},
			dayRevenueData: function() {
				var businessId = '{{ $user->profile->id }}';
				var type = "revenue";
				this.getDataLine(businessId, type);
			},
			weekInteractionData: function() {
				var businessId = '{{ $user->profile->id }}';
				var timeSpan = "week";
				var type = "interaction";
				this.getDataBar(businessId, timeSpan, type);
			},
			monthInteractionData: function() {
				var businessId = '{{ $user->profile->id }}';
				var timeSpan = "month";
				var type = "interaction";
				this.getDataBar(businessId, timeSpan, type);
			},
			twoMonthInteractionData: function() {
				var businessId = '{{ $user->profile->id }}';
				var timeSpan = "2month";
				var type = "interaction";
				this.getDataBar(businessId, timeSpan, type);
			},
			weekRevenueData: function() {
				var businessId = '{{ $user->profile->id }}';
				var timeSpan = "week";
				var type = "revenue";
				this.getDataBar(businessId, timeSpan, type);
			},
			monthRevenueData: function() {
				var businessId = '{{ $user->profile->id }}';
				var timeSpan = "month";
				var type = "revenue";
				this.getDataBar(businessId, timeSpan, type);
			},
			twoMonthRevenueData: function() {
				var businessId = '{{ $user->profile->id }}';
				var timeSpan = "2month";
				var type = "revenue";
				this.getDataBar(businessId, timeSpan, type);
			},
			getDataLineHour: function(businessId, type) {
				$.ajax({
					method: 'POST',
					url: '/analytics/dashboard/data/line/hour',
					data: {
						'businessId': businessId,
						'type': type
					},
					success: data => {
						console.log(data);
						var type = data.type;
						var dataSet = data.data;

						if (type === 'interaction') {
							this.postsActivityByHour = dataSet;
							var lineInteractionsHour = $("#lineInterHour").get(0).getContext("2d");
				    	var lineInteractionsHourData = this.formatLineDataHour(this.postsActivityByHour, type);
				    	var lineChartInterHour = new Chart(lineInteractionsHour, {
				    		type: 'line',
				    		data: lineInteractionsHourData,
				    		options: lineChartOptions
				    	});
						} else {
							this.postsRevenueByHour = dataSet;
							var lineRevenueHour = $("#lineRevenueHour").get(0).getContext("2d");
				    	var lineRevenueHourData = this.formatLineDataHour(this.postsRevenueByHour, type);
				    	var lineChartRevenueHour= new Chart(lineRevenueHour, {
				    		type: 'line',
				    		data: lineRevenueHourData,
				    		options: lineChartOptions
				    	});
						}
					}
				})
			},
			getDataLine: function(businessId, type) {
				$.ajax({
					method: 'POST',
					url: '/analytics/dashboard/data/line',
					data: {
						'businessId': businessId,
						'type': type
					},
					success: data => {
						var type = data.type;
						var dataSet = data.data;

						if (type === 'interaction') {
							this.postsActivityByDay = dataSet;
							var lineInteractionsDay = $("#lineInterDay").get(0).getContext("2d");
				    	var lineInteractionsDayData = this.formatLineData(this.postsActivityByDay, type);
				    	var lineChartInter = new Chart(lineInteractionsDay, {
				    		type: 'line',
				    		data: lineInteractionsDayData,
				    		options: lineChartOptions
				    	});
						} else {
							this.postsRevenueByDay = dataSet;
							var lineRevenueDay = $("#lineRevenueDay").get(0).getContext("2d");
				    	var lineRevenueDayData = this.formatLineData(this.postsRevenueByDay, type);
				    	var lineChartRevenue= new Chart(lineRevenueDay, {
				    		type: 'line',
				    		data: lineRevenueDayData,
				    		options: lineChartOptions
				    	});
						}
					}
				})
			},
			getDataBar: function(businessId, timeSpan, type) {
				$.ajax({
					method: 'POST',
					url: '/analytics/dashboard/data/bar',
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
									var barInteractionsWeekRaw = $("#barInteractionsWeek").get(0);
									var barInteractionsWeek = barInteractionsWeekRaw.getContext("2d");
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
						    		options: barChartOptionsRevenue
						    	});
								}
								break;
							case "month":
								if (type === 'interaction') {
									this.postsInteractedMonth = dataSet;
									var barInteractionsMonthRaw = $("#barInteractionsMonth").get(0);
									var barInteractionsMonth = barInteractionsMonthRaw.getContext("2d");
									var barInteractionsMonthData = this.formatBarData(this.postsInteractedMonth, type);
									var barChartInter30 = new Chart(barInteractionsMonth, {
						    		type: 'bar',
						    		data: barInteractionsMonthData,
						    		options: barChartOptions
						    	});

						    	barInteractionsMonthRaw.onclick = function(evt) {
						    		var activePoints = barChartInter30.getElementsAtEvent(evt);
						    		var idx = activePoints[0]['_index'];
						    		console.log(idx);
						    	};
								} else {
									this.postsRevenueMonth = dataSet;
									var barRevenueMonth = $("#barRevenueMonth").get(0).getContext("2d");
									var barRevenueMonthData = this.formatBarData(this.postsRevenueMonth, type);
									
						    	var barChartRevenue30 = new Chart(barRevenueMonth, {
						    		type: 'bar',
						    		data: barRevenueMonthData,
						    		options: barChartOptionsRevenue
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
						    		options: barChartOptionsRevenue
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