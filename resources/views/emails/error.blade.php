<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>
			Error Notification
		</title>
	</head>
	<body>
		<h3>Customer: {{ $customer->first_name }} {{ $customer->last_name }}</h3>
		<h4>Email: {{ $customer->email }}</h4>
		<hr>
		<h3>Business: {{ $profile->business_name }}</h3>
		<h4>Business Phone: {{ $profile->account->phone }}</h4>
		<hr>
		<h3>Error: {{ $msg }}</h3>
		<h3>Code: {{ $code }}</h3>
		@if($splashId != 0)
			<h4>Splash ID: {{ $splashId }}</h4>
		@endif
		<h4>Transaction ID: {{ $transaction->id }}</h4>
	</body>
</html>