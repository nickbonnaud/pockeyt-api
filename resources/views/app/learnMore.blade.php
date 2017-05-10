@extends('layout')
@section('content')
	<div class="learn_top">
		<h3>Automatically Sync your Pockeyt transactions with QuickBooks</h3>
		<p>Keep your QuickBooks account current and up-to-date with Pockeyt Sync</p>
	</div>
	<div class="learn-middle">
		<img src="{{ asset('/images/sync-image.png') }}">
	</div>
	<div class="learn-bottom">
		<p>To connect your Pockeyt account, please go to the <a href="{{ route('app.index') }}">Pockeyt Dashboard</a> and click Quickbooks in your Inventory tab.</p>
	</div>
@stop