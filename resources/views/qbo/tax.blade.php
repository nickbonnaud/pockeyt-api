@extends('layoutDashboard')
@section('content')
<div class="content-wrapper">
	<section class="content">
		<div style="text-align: center">
			<h2>Success! Connected to QuickBooks Online</h2>
			<h3>Warning</h3>
			@if($qbTaxRate == 'not set')
			<h4>Your current Sales Tax Rate in QuickBooks is <strong>not set</strong>.</h4>
			<h4>Please set your Sales Tax Rate in QuickBooks to <strong>{{ $pockeytTaxRate }}%</strong> if that is the correct value for your location. In order for Pockeyt to sync with Quickbooks, your Sales Taxes must match.</h4>
			@else
			<h4>Your current Sales Tax Rate in QuickBooks does <strong>not match</strong> your Sales Tax in Pockeyt.</h4>
			<h4>Please set your Sales Tax Rate in QuickBooks to <strong>{{ $pockeytTaxRate }}%</strong> if that is the correct value for your location. In order for Pockeyt to sync with Quickbooks, your Sales Taxes must match.</h4>
			@endif
			<p>Please correct by adjusting your Sales Tax in QuickBooks or adjusting your location in Pockeyt*.</p>
			<p>Once Sales Taxes are matching click the Set Sales Tax Button in you Business Account Profile Section to finish setting up Pockeyt Sync.</p>
			<button type="button" class="btn btn-block btn-primary btn-lg" onclick="self.close()">Close Window</button>
			<p>*Pockeyt automatically sets your Sales Tax based on your businesses location.</p>
		</div>
	</section>
</div>
@stop

@section('scripts.footer')
<script type="text/javascript">
  window.opener.location.reload(false);
</script>