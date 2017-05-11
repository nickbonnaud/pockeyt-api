@extends('layout')
@section('content')
  <div class="container" style="background: #ecf0f5; border-radius: 20px; padding: 50px;">
		<div class="learn-top">
			<h3>Automatically sync your Pockeyt transactions with QuickBooks.</h3>
		</div>
		<div class="row">
			<div class="learn-middle">
				<div class="col-md-6">
					<p>Keep your QuickBooks account current and up-to-date with Pockeyt Sync.</p>
					<img src="{{ asset('/images/sync-image.png') }}">
					<h4>Features:</h4>
					<ul>
						<li>Syncs every transaction on Pockeyt to your QuickBooks Account.</li>
						<li>Every transaction synced creates an invoice and closes the Invoice once payment confirmed.</li>
						<li>Pockeyt easily tracks sales, tips, and taxes and automatically syncs them with your QuickBooks account for every transaction.</li>
						<li>Seamless and automatic once connected!</li>
					</ul>
				</div>
				<div class="col-md-6">
					<img src="{{ asset('/images/qboConnectScreen.png') }}">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="learn-bottom">
				<p>To connect to your Pockeyt account, please go to the <a href="{{ route('app.index') }}">Pockeyt Dashboard</a> and click QuickBooks in your Inventory tab.</p>
			</div>
		</div>
	</div>
@stop