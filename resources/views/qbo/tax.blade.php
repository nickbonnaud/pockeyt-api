@extends('layoutDashboard')
@section('content')
<div class="content-wrapper">
	<section class="content">
		<div style="text-align: center">
			<h2>Success! Connected to QuickBooks Online</h2>
			<h4>Warning</h4>
			<h4>Your current Sales Tax Rate in Pockeyt is {{ $pockeytTaxRate }}%. Your Sales Tax Rate in QuickBooks is {{ $qbTaxRate }}%. In order for Pockeyt to sync with Quickbooks, your Sales Taxes must match.</h4>
			<p>Please correct by adjusting your Sales Tax in QuickBooks or adjusting your location in Pockey*.</p>
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