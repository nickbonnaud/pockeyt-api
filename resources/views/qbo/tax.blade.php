<html>
<head>
  <title>Sales Tax Not Found</title>
</head>
<body>

<div style="text-align: center; font-family: sans-serif; font-weight: bold;">
<b>Ooops!</b>
	Your current Sales Tax Rate for Pockeyt Pay is {{ ($user->profile->tax_rate) / 100 }}% based on the location where you collect Pockeyt Payments.
	<p>This Sales Tax Rate does not match your current Sales Tax Rate in Quickbooks! Please adjust your location in Pockeyt or your Sales Tax Rate in Quickbooks.</p>
</div>

<script type="text/javascript">
  window.opener.location.reload(false);
</script>
 @yield('scripts.footer')
  @include('flash')
</body>
</html>