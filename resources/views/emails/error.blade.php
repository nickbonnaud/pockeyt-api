<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>
			Error Notification
		</title>
	</head>
	<body>
		<h4>Customer: {{ $customer->first_name }} {{ $customer->last_name }}</h4>
		<h3>Email: {{ $customer->email }}</h3>
		<hr>
		<h4>Business: {{ $profile->business_name }}</h4>
		<h3>Business Phone: {{ $profile->account->phone }}</h3>
		<hr>
		<h4>Error: {{ $msg }}</h4>
		<h4>Code: {{ $code }}</h4>
		@if($splashId != 0)
			<h3>Splash ID: {{ $splashId }}</h3>
		@endif
		<h3>Transaction ID: {{ $transaction->id }}</h3>
	</body>
</html>