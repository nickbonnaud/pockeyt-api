@extends('layoutDashboard')
@section('content')
<div class="content-wrapper">
	<section class="content">
		<div style="text-align: center">
			<h4>Please Adjust your Sales Tax Rate in QuickBoooks or Pockeyt.</h4>
			<button type="button" class="btn btn-block btn-primary btn-lg" onclick="self.close()">Close Window</button>
		</div>
	</section>
</div>
@stop

@section('scripts.footer')
<script type="text/javascript">
  window.opener.location.reload(false);
</script>