@extends('layoutDashboard')

@if(is_null($profile))

<div class="row">
    <div class="col-md-12">
        <h2 class="text-center">Profile not found.</h2>
    </div>
</div>

@else

    @section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Customer Dashboard
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content" id="customer">
    <!-- Default box -->
    <div>
      <template v-for="user in users">
        <div class="col-sm-4 col-md-3">
          <div class="box box-primary">
            <div class="box-header with-border text-center">
              <a v-on:click="getCustomerPurchases(user.id)" class="customer-name-title" href="#" data-toggle="modal" data-target="#CustomerinfoModal">
                <h3 class="box-title">@{{user.first_name}} @{{user.last_name}}</h3>
              </a>
              <div class="box-body">
                <a v-on:click="getCustomerPurchases(user.id)"  href="#" data-toggle="modal" data-target="#CustomerinfoModal">
                  <img :src="user.photo_path" class="profile-user-img img-responsive img-circle" alt="User Image">
                </a>
              </div>
              <div class="box-footer">
                <a v-on:click="goToTransaction(user.id)" class="btn btn-primary btn-block">
                  <b>Bill</b>
                </a>
                <div v-if="checkForDeal(user.id)">
                  <a href="#" data-toggle="modal" data-target="#redeemDealModal" class="btn btn-success btn-block btn-redeem">
                    <b>Redeem Deal</b>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="CustomerinfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header-timeline">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="CustomerinfoModal">@{{user.first_name}} @{{user.last_name | setPossessive}} recent purchases</h4>
              </div>
              <div class="modal-body-timeline">
                <ul class="timeline col-sm-4 col-md-4">
                  <!-- timeline time label -->
                  <li class="time-label" style="margin-top: -34px">
                    <span class="bg-blue">
                      @{{ moment().format("Do MMM YY") }}
                    </span>
                  </li>
                  <li v-for="purchase in purchases" v-bind:style="transactionDistance(purchase)">
                    <!-- timeline icon -->
                    <i class="fa fa-money bg-green"></i>
                    <div class="timeline-item">
                      <h3 class="timeline-header">@{{ purchase.updated_at | setDate }}</h3>
                    </div>
                  </li>
                  <li style="top : 97%">
                    <i class="fa fa-clock-o bg-gray"></i>
                  </li>
                </ul>
                
                <div class="scroll-container-timeline col-sm-8 col-md-8">
                  <div class="scroll-contents">
                    <div v-for="purchase in purchases">
                      <div class="box box-primary">
                        <div class="box-header with-border text-center">
                          <h3 class="box-title">Purchase on the @{{ purchase.updated_at | setDate }}</h3>
                        </div>
                        <div class="box-body">
                          <purchases :products="purchase.products"></purchases>
                        </div>
                        <div class="box-footer timeline-list-footer">
                          <div class="pull-right"><b>Total: $@{{ purchase.total / 100 }}</b></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="redeemDealModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header-timeline">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="redeemDealModal">@{{user.first_name}} @{{user.last_name | setPossessive}} purchased Deal</h4>
              </div>
              <div class="modal-body">
                <div class="box-body">
                  <div v-for="deal in deals">
                    <div v-if="deal.user_id === user.id">
                      <span class="pull-left">
                        <h3 class="deal-item">@{{ deal.products | getDealItem }}</h3>
                      </span>
                      <span class="pull-right">
                        <button class="btn btn-block btn-success btn-sm pull-right">Redeem!</button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </template>
    </div>
    <!-- /.box -->
  </section>
  <!-- /.content -->
</div>

<!-- /.content-wrapper -->
@stop
@endif

@section('scripts.footer')
    <script src="//js.pusher.com/3.2/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>

    <script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var prevDistance = {
      'lastDist' : 0,
      'padding' : 0,
    }

      Vue.component('purchases', {
        props: ['products'],
        template: '<div><div v-for="item in items"><p class="timeline-purchases-left">@{{ item.quantity }} x @{{ item.name }}</p><p class="timeline-purchases-right">$@{{ (item.price / 100) }}</p></div></div>',
        data: function() {
          return {
            items: JSON.parse(this.products)
          }
        },
      });

      var customer = new Vue({
        el: '#customer',

        data: {
          users: [],
          purchases: [],
          deals: []
        },

        mounted: function() {
          var pusher = new Pusher('f4976d40a137b96b52ea', {
            encrypted: true
          });

          pusher.subscribe("{!! 'business' . $profile->id !!}")
            .bind('App\\Events\\CustomerEnterRadius', this.addUser);

          pusher.subscribe("{!! 'customerAdd' . $profile->id !!}")
            .bind('App\\Events\\CustomerLeaveRadius', this.removeUser);

          window.setInterval(this.removeInactiveUser, 120000);
        },

        filters: {
          setDate: function(value) {
            date = moment(value).format("Do MMM YY");
            return date;
          },
          setPossessive: function(value) {
            if (value.endsWith('s')) {
              return value.concat("'");
            } else{
              return value.concat("'s");
            }
          },
          getDealItem: function(value) {
            dealItem = JSON.parse(value);
            return dealItem[0].name;
          }
        },

        methods: {

          transactionDistance: function(purchase) {
            var mostRecent = Date.parse(this.purchases[0].updated_at);
            var last = Date.parse(this.purchases[this.purchases.length - 1].updated_at);
            var totalDistance = mostRecent - last;
            var relativeDistance = Math.round(((mostRecent - Date.parse(purchase.updated_at)) / totalDistance) * 100);
            if (relativeDistance > 94) {
              relativeDistance = relativeDistance - 6;
            }
            if (((relativeDistance - prevDistance.lastDist) < 3) && (purchase.id !== this.purchases[0].id)) {
              prevDistance.lastDist = relativeDistance;
              prevDistance.padding = prevDistance.padding + 20;
              return {top: relativeDistance.toString() + '%', 'padding-top': prevDistance.padding.toString() + 'px'}
            } else {
              prevDistance.lastDist = relativeDistance;
              prevDistance.padding = 0;
              return {top: relativeDistance.toString() + '%'}
            }
          },

          addUser: function(data) {
            var activeCustomer = data.user;
            var users = this.users;
            var purchases = this.purchases;

            if(users.length == 0) {
              activeCustomer['lastActive'] = Date.now();
              users.push(activeCustomer);
            } else {
              for (i=users.length - 1; i >= 0; i --) {
                if(!users[i].id == activeCustomer.id) {
                  activeCustomer['lastActive'] = Date.now();
                  users.push(activeCustomer);
                } else if (users[i].id == activeCustomer.id) {
                  users[i].lastActive = Date.now();
                }
              }
            }
            this.getRedeemableDeals(activeCustomer.id);
          },
          removeUser: function(user) {
            var leavingCustomer = user.user;
            var users = this.users;
            
            if(users.length > 0) {
              for (i=users.length - 1; i >= 0; i --) {
                if (users[i].id == leavingCustomer.id) {
                  users.splice(i, 1);
                }
              }
            }
          },
          removeInactiveUser: function() {
            var users = this.users;
            if (users.length > 0) {
              for (i=users.length - 1; i >= 0; i --) {
                var userLastActive = users[i].lastActive;
                var currentTime = Date.now();
                if (currentTime - userLastActive >= 120000) {
                  var businessId = '{{ $profile->id }}'
                  this.deleteInactiveUser(users[i].id, businessId);
                  users.splice(i, 1);
                }
              }
            }
          },
          goToTransaction: function(customerId) {
            route = "{{ route('bill.show', ['customerId' => 'id']) }}"
            location.href = route.replace('id', customerId)
          },
          deleteInactiveUser: function(customerId, businessId) {
            $.ajax({
              method: 'POST',
              url: '/geo/user/destroy',
              data: {
                'customerId' : customerId,
                'businessId' : businessId
              }
            })
          },
          moment: function() {
            return moment();
          },
          getCustomerPurchases: function(customerId) {
            var businessId = '{{ $profile->id }}'

            $.ajax({
              method: 'POST',
              url: '/user/purchases',
              data: {
                'customerId' : customerId,
                'businessId' : businessId
              },
              success: data => {
                this.purchases = data
              }
            })
          },
          getRedeemableDeals: function(customerId) {
            var businessId = '{{ $profile->id }}'
            $.ajax({
              method: 'POST',
              url: '/user/deals',
              data: {
                'customerId' : customerId,
                'businessId' : businessId
              },
              success: data => {
                var deals = this.deals;
                if (data.length > 0 ) {
                  data.forEach(function (userDeal) {
                    var found = false;
                    if (deals.length > 0) {
                      deals.forEach(function(currentUserDeals) {
                        if (currentUserDeals.id === userDeal.id) {
                          found = true;
                        }
                      }); 
                      if (found === false) {
                        deals.push(userDeal)
                      }
                    } else {
                      deals.push(userDeal)
                    }
                  });
                }
              } 
            })
          },
          checkForDeal(userId) {
            if (this.deals.length > 0 ) {
              var found = false;
              this.deals.forEach(function(e) {
                if (e.user_id === userId) {
                  found = true;
                }
              });
              return found;
            }
          }
        }
      })
    </script>
@stop






