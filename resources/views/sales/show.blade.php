@extends('layoutDashboard')

@section('content')
<div id="sales">
	<div class="content-wrapper-scroll">
		<div class="scroll-main">
			<div class="scroll-main-contents">
				<section class="content-header">
			    <h1>
			      Sales Center
			    </h1>
			    <h4 v-if="fromDate == 'today'">Date Range: Today</h4>
			    <h4 v-else>Date Range: @{{ fromDate | setDate }} to @{{ toDate | setDate }}</h4>
			    <a href="#" v-on:click="toggleDate()">Change</a>
			    <input v-show="customDate" type="text" name="daterange" value="01/01/2015 - 01/31/2015" />
			    <ol class="breadcrumb">
			      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
			      <li class="active">Sales Center</li>
			    </ol>
			  </section>
				<section class="content">
					<div class="scroll-container-analytics">
						<div class="scroll-contents">
							<div class="row">
								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
									<div class="small-box bg-green">
										<div class="inner">
											<h3>$@{{ netSales }}</h3>
											<p>Net Sales</p>
										</div>
										<div class="icon"><i class="fa fa-usd"></i></div>
										<a href="#" class="small-box-footer" v-on:click="modalNetSales()">More info <i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
									<div class="small-box bg-red">
										<div class="inner">
											<h3>$@{{ netTaxes }}</h3>
											<p>Net Sales Tax</p>
										</div>
										<div class="icon"><i class="fa fa-balance-scale"></i></div>
										<a href="#" class="small-box-footer" v-on:click="modalSalesTax()">More info <i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
									<div class="small-box bg-aqua">
										<div class="inner">
											<h3 class="analytics-bubble">$@{{ netTips }}</h3>
											<p>Net Tips</p>
										</div>
										<div class="icon"><i class="fa fa-thumbs-o-up"></i></div>
										<a href="#" class="small-box-footer" v-on:click="modalTips()">More info <i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
									<div class="small-box bg-green">
										<div class="inner">
											<h3 class="analytics-bubble">$@{{ netTotal }}</h3>
											<p>Net Total</p>
										</div>
										<div class="icon"><i class="fa fa-money"></i></div>
										<a href="#" class="small-box-footer" v-on:click="modalTotal()">More info <i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
							</div>
							<div class="row">
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
	<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header-analytics best_hour">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h3 class="modal-title" id="infoModal">@{{ modalPick }}</h3>
	      </div>
	      <div class="modal-body-analytics-bubble">
	      	<div class="sub-header">
        		<h3 v-if="fromDate == 'today'">@{{ modalPick }} for Today: <strong>$@{{ modalPickData }}</strong>.</h3>
        		<h3 v-else>@{{ modalPick }} from @{{ fromDate }} to @{{ toDate }}: <strong>$@{{ modalPickData }}</strong>.</h3>
        	</div>
	      </div>
	    </div>
	  </div>
	</div>
</div>
@stop

@section('scripts.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script>

	$(function() {
    $('input[name="daterange"]').daterangepicker();
});

	var sales = new Vue({
		el: '#sales',

		data: {
			transactions: {!! $salesToday !!},
			fromDate: "today",
			toDate: "",
			modalPick: "",
			modalPickData: "",
			dateRange: "",
			customDate: false
		},

		mounted: function() {},

		computed: {
			netSales: function() {
				var transactions = this.transactions;
				if (transactions == 0) { return 0}
				var total = 0;
				transactions.forEach(function(transaction) {
					total = total + transaction.net_sales;
				});
				return (total / 100).toFixed(2);
			},
			netTaxes: function() {
				var transactions = this.transactions;
				if (transactions == 0) { return 0}
				var total = 0;
				transactions.forEach(function(transaction) {
					total = total + transaction.tax;
				});
				return (total / 100).toFixed(2);
			},
			netTips: function() {
				var transactions = this.transactions;
				if (transactions == 0) { return 0}
				var total = 0;
				transactions.forEach(function(transaction) {
					total = total + transaction.tips;
				});
				return (total / 100).toFixed(2);
			},
			netTotal: function() {
				var transactions = this.transactions;
				if (transactions == 0) { return 0}
				var total = 0;
				transactions.forEach(function(transaction) {
					total = total + transaction.total;
				});
				return (total / 100).toFixed(2);
			},
		},

		filters: {
			setDate: function(value) {
	      date = moment(value).format("Do MMM YY");
	      return date;
	    }
		},

		methods: {
			modalNetSales: function() {
				this.modalPick = "Net Sales";
				this.modalPickData = this.netSales;
				$('#infoModal').modal('show');
			},
			modalSalesTax: function() {
				this.modalPick = "Net Sales Taxes";
				this.modalPickData = this.netTaxes;
				$('#infoModal').modal('show');
			},
			modalTips: function() {
				this.modalPick = "Net Tips";
				this.modalPickData = this.netTips;
				$('#infoModal').modal('show');
			},
			modalTotal: function() {
				this.modalPick = "Net Total";
				this.modalPickData = this.netTotal;
				$('#infoModal').modal('show');
			},

			toggleDate: function() {
				this.customDate = !this.customDate;
				console.log(this.customDate);
			},

			getTransactions: function(fromDate, toDate) {
				$.ajax({
					method: 'POST',
					url: '/analytics/dashboard/data/line/hour',
					data: {
						'fromDate': fromDate,
						'toDate': toDate
					},
					success: function(data) {
						console.log(data);
						var type = data.type;
					}
				})
			}
		}
	})
</script>
@stop