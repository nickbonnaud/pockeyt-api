@extends('layoutDashboard')

@section('content')

<div class="content-wrapper">
  <section class="content-header">
    <h1 class="header-button">
      {{$customer->first_name}} {{$customer->last_name}}'s Bill
    </h1>
    <button data-toggle="modal" data-target="#customItem" type="button" class="btn btn-primary btn-sm custom-amount-btn">Custom Amount</button>
    <ol class="breadcrumb">
      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Bill</li>
    </ol>
  </section>

  <section class="content" id="inventory">
    <form class="product-search">
      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-search"></i></span>
        <input style="font-size: 28px;" type="text" name="query" class="form-control" placeholder="Search" v-model="query">
      </div>
    </form>
    @include ('errors.form')
    <div class="scroll-container col-md-8">
      <div class="scroll-contents">
        @include('partials.transactions.inventory')  
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
          <div class="tax-section">
            <span>Tax:</span>
            <span class="pull-right">$@{{ (totalTax / 100).toFixed(2) }}</span>
          </div>
          <b>Total:</b>
          <div class="receipt-total">
            <b>$@{{ (totalBill / 100).toFixed(2) }}</b>
          </div>
        </div>
        <div v-if="bill.length !== 0">
          @include ('partials.transactions.charge_existing')
        </div>
      </div>
    </div>
    <div class="modal fade" id="customItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header-timeline">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title" id="customItem">Custom Amount</h3>
          </div>
          <div class="modal-body-custom-amount">
            <section class="content custom-amount">
              <div class="col-md-12">
                <form class="form-horizontal">
                  <div class="form-group" style="margin-left: 15%;">
                    <label for="inputName" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                      <input v-model="name" name="name" type="text" class="form-control" style="width: 50%;" id="inputName" placeholder="Name" required>
                    </div>
                  </div>
                  <div class="form-group" style="margin-left: 15%;">
                    <label for="inputPrice" class="col-sm-2 control-label">Price</label>
                    <div class="col-sm-10">
                      <input v-model="price" type="number" name="price" class="form-control" style="width: 50%;" id="inputPrice" placeholder="Price" pattern="^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$" step="any" required>
                    </div>
                  </div>
                  <button v-bind:disabled="(name == '' || price == '')" type="button" class="btn btn-block btn-primary" v-on:click="addCustomProduct()">Add</button>
                </form>
              </div>
            </section>
          </div>
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
        inventory: {!! $inventory !!},
        bill: currentBill.fetch(),
        name: '',
        price: '',
        saved: false,
        query: ''
      },

      mounted: function() {
         $(window).on('beforeunload', this.leaving);
      },

      filters: {
        truncate: function(string, value) {
          if (string.length > 20) {
            return string.substring(0, 20) + '...';
          } else {
            return string;
          }
        }
      },

      computed: {
        subTotal: function() {
          var bill = this.bill;
          var total = 0;
          bill.forEach(function(product) {
            total = total + (product.quantity * product.price)
          });
          return total;
        },

        totalTax: function() {
          var tax = this.subTotal * {{ ($user->profile->tax_rate) / 10000 }};
          return tax;
        },

        totalBill: function() {
          var total = this.subTotal + this.totalTax;
          return total;
        },

        productsFilter: function() {
          return this.findBy(this.inventory, this.query, 'name');
        }
      },

      methods: {

        findBy: function(list, value, column) {
          return list.filter(function(product) {
            return product[column].toLowerCase().includes(value.toLowerCase());
          });
        },

        addCustomProduct: function() {
          var product = {
            quantity: 1,
            name: this.name,
            price: this.price * 100
          };
          this.bill.push(product);
          this.name = '';
          this.price = '';
          $('#customItem').modal('hide');
        },

        addProduct: function(product) {
          console.log(product);
          var bill = this.bill;
          var result = $.grep(bill, function(item) { return item.id === product.id});
          if (result.length === 0) {
            product['quantity'] = 1;
            bill.push(product);
          } else {
            result[0].quantity++
            console.log(result[0]);
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







