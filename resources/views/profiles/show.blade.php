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
        <div class="col-md-3">
          <div class="box box-primary">
            <div class="box-header with-border text-center">
              <a class="customer-name-title" href="#" data-toggle="modal" data-target="#CustomerinfoModal">
                <h3 class="box-title">@{{user.first_name}} @{{user.last_name}}</h3>
              </a>
              <div class="box-body">
                <a href="#" data-toggle="modal" data-target="#CustomerinfoModal">
                  <img :src="user.photo_path" class="profile-user-img img-responsive img-circle" alt="User Image">
                </a>
              </div>
              <div class="box-footer">
                <a v-on:click="goToTransaction(user.id)" class="btn btn-primary btn-block">
                <b>Bill</b>
              </a>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="CustomerinfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="CustomerinfoModal">@{{user.first_name}} @{{user.last_name}}</h4>
            </div>
            <div class="modal-body">
              <ul class="timeline">
                <!-- timeline time label -->
                <li class="time-label">
                  <span class="bg-red">
                      10 Feb. 2014
                  </span>
                </li>
                <!-- /.timeline-label -->
                <!-- timeline item -->
                <li>
                  <!-- timeline icon -->
                  <i class="fa fa-envelope bg-blue"></i>
                  <div class="timeline-item">
                    <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>
                    <h3 class="timeline-header"><a href="#">Support Team</a> ...</h3>
                    <div class="timeline-body">

                    </div>
                    <div class="timeline-footer">
                      <a class="btn btn-primary btn-xs">...</a>
                    </div>
                  </div>
                </li>
                <li>
                  <i class="fa fa-clock-o bg-gray"></i>
                </li>
              </ul>
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
      var customer = new Vue({
        el: '#customer',

        data: {
          users: [],
          purchases: []
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

        methods: {
          addUser: function(data) {
            var activeCustomer = data.user;
            var transactions = data.transactions;
            var users = this.users;
            var purchases = this.purchases;

            if(users.length == 0) {
              activeCustomer['lastActive'] = Date.now();
              transactions.forEach(function(transaction) {
                purchases.push(transaction);
              });
              console.log(purchases);
              users.push(activeCustomer);
            } else {
              for (i=users.length - 1; i >= 0; i --) {
                if(!users[i].id == activeCustomer.id) {
                  activeCustomer['lastActive'] = Date.now();
                  transactions.forEach(function(transaction) {
                    purchases.push(transaction);
                  });
                  console.log(purchases);
                  users.push(activeCustomer);
                } else if (users[i].id == activeCustomer.id) {
                  users[i].lastActive = Date.now();
                }
              }
            }
          },
          removeUser: function(user) {
            var leavingCustomer = user.user;
            var users = this.users;
            
            if(users.length > 0) {
              for (i=users.length - 1; i >= 0; i --) {
                if (users[i].id == leavingCustomer.id) {
                  this.removeUserTransactions(users[i].id);
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
                  this.removeUserTransactions(users[i].id);
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
          removeUserTransactions: function(userId) {
            var purchases = this.purchases;

            if(purchases.length > 0) {
              for (i=purchases.length - 1; i >= 0; i --) {
                if(purchases[i].user_id == userId) {
                  purchases.splice(i, 1);
                }
              }
            }
          }
        }
      })
    </script>
@stop






