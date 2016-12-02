@extends('layoutDashboard')

@section('content')

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      {{$customer->first_name}} {{$customer->last_name}}'s Bill
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Bill</li>
    </ol>
  </section>

  <section class="content" id="inventory">
    @include ('errors.form')
    <div class="scroll-container col-md-8">
      <div class="scroll-contents">
        @include('partials.transactions.inventory', ['inventory' => $inventory])  
      </div>
    </div>
    <div class="col-md-4">
      <div class="box box-black">
        <div class="box-header with-border">
          <h3 class="box-title">{{$customer->first_name}}'s Receipt</h3>
          <div class="pull-right" v-if="bill.length !== 0">
            @include ('partials.transactions.update')
          </div>
        </div>
        <div class="box-body no-padding">
          <table class="table table-striped">
            <tbody>
              <tr>
                <th>Quantity</th>
                <th>Name</th>
                <th>Price</th>
                <th></th>
              </tr>
              @include('partials.transactions.bill_list')
            </tbody>
          </table>
        </div>
        <div class="box-footer-receipt">
          <b>Total:</b>
          <div class="receipt-total">
            <b>$@{{ (totalBill / 100).toFixed(2) }}</b>
          </div>
        </div>
        <div v-if="bill.length !== 0">
          @include ('partials.transactions.charge')
        </div>
      </div>
    </div>
  </section>

</div>
@stop

@section('scripts.footer')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
  <script>

    var currentBill = {
      fetch: function() {
        var bill = JSON.parse('{!! $bill !!}');
        return bill;
      }
    }

    var inventory = new Vue({
      el: "#inventory",

      data: {
        bill: currentBill.fetch(),
        saved: false
      },

      mounted: function() {
         $(window).on('beforeunload', this.leaving);
      },

      computed: {
        totalBill: function() {
          var bill = this.bill;
          var total = 0;
          bill.forEach(function(product) {
            total = total + (product.quantity * product.price)
          });
          return total;
        }
      },

      methods: {
        addProduct: function(product) {
          var bill = this.bill;
          var result = $.grep(bill, function(item) { return item.id === product.id});
          if (result.length === 0) {
            product['quantity'] = 1;
            bill.push(product);
          } else {
            result[0].quantity++
          }
        },
        subtractProduct: function(product) {
          var bill = this.bill;
          var result = $.grep(bill, function(item) { return item.id === product.id});
          if (result[0].quantity !== 1) {
            result[0].quantity--
          } else {
            for(var i = 0; i < bill.length; i++) {
              if(bill[i].id == product.id) {
                bill.splice(i, 1);
                break;
              }
            }
          }
        },
        save: function() {
          this.saved = true;
        },
        leaving: function() {
          if(! this.saved) {
            return 'Warning: Bill not saved. Hit "Keep Open" button to save!';
          }
        }
      }
    })
  </script>
@stop







