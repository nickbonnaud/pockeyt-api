@extends('layoutDashboard')

@section('content')

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      {{$customer->first_name}} {{$customer->last_name}}'s bill
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Bill</li>
    </ol>
  </section>

  <section class="content" id="inventory">
    <div class="scroll-container col-md-9">
      <div class="scroll-contents">
        @include('partials.transactions.inventory', ['inventory' => $inventory])  
      </div>
    </div>
  </section>

</div>
@stop

@section('scripts.footer')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
  <script>

    var inventory = new Vue({
      el: "#inventory",

      data: {
        bill: []
      },

      methods: {
        addProduct: function(product) {
          var this.bill = bill
          var result = $.grep(bill, function(item) { return item.id === product.id});
          if (result.length === 0) {
            bill.push(product);
          }
          console.log(bill);
        }
      }
    })


  </script>
@stop







