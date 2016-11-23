@extends('layoutDashboard')

@section('content')

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      {{$customer->first_name}} {{$customer->last_name}}'s bill
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Transaction</li>
    </ol>
  </section>

  <section class="content" id="inventory">
    <p>{{ $business->id }}</p>
    <products></products>
  </section>
</div>

<section class="content">
  <div class="scroll-container col-md-9">
    <div class="scroll-contents">
      @include('partials.transactions.inventory', ['inventory' => $inventory])  
    </div>
  </div>
</section>

@stop








