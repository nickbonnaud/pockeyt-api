@extends('layout')
@section('content')
	<h3>Successfully Disconnected Pockeyt</h3>
	<p>Pockeyt is no longer connected to your Quickbooks account and will no longer auto-update transactions.</p>
	<p>To reconnect, please go to the <a href="{{ route('app.index') }}" target="_blank">Pockeyt Dashboard</a> and reconnect Quickbooks in your Inventory tab.</p>
	
@stop



