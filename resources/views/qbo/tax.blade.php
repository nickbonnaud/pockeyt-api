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
<script src="{{ asset('/vendor/jquery/jquery-2.2.3.min.js') }}"></script>
	<script src="{{ asset('/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('/vendor/slimScroll/jquery.slimscroll.min.js') }}"></script>
	<script src="{{ asset('/vendor/fastclick/fastclick.js') }}"></script>
	<script src="{{ asset('/js/app.min.js') }}"></script>
	<script src="{{ asset('/vendor/jqueryui/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('/vendor/vue/vue.min.js') }}"></script>
  <script src="{{ asset('/vendor/sweetalert/dist/sweetalert.min.js') }}"></script>
  <script src="{{ asset('/vendor/moment/min/moment.min.js') }}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2-rc.1/js/select2.min.js"></script>
	@yield('scripts.footer')
  @include('flash')
</body>
</html>