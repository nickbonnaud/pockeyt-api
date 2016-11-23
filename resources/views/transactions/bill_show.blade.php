@extends('layoutDashboard')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Customer Dashboard
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Transaction</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="box-header with-border">
      <h3 class="box-title">Pizza</h3>
    </div>
    <div class="box-body">
      Hello
    </div>
  
  <!-- <tasks inventory=""></tasks> -->

  </section>
</div>

<template id="products-template">
  
</template>

@stop
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script src="/js/vuejs.js"></script>








