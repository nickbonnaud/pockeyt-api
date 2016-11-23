@extends('layoutDashboard')

@section('content')

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Customer Dashboard
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Transaction</li>
    </ol>
  </section>

  <section class="content" id="inventory">
    <products></products>
  </section>
</div>

<template id="products-template">
  <div class="col-md-3" v-for="product in inventory">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Pizza</h3>
      </div>
      <div class="box-body">
        Hello
      </div>
    </div>
  </div>
</template>

@stop
@section('scripts.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script src="/js/vuejs.js"></script>
@stop








