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
            <section class="content">

              <!-- Default box -->
              <div id="customer">
                <template v-for="user in users">
                  <div class="col-md-6">
                    <div class="box box-primary">
                      <div class="box-header with-border text-center">
                        <h3 class="box-title">@{{user.first_name}} @{{user.last_name}}</h3>
                        <div class="box-body">
                        <img src="@{{ user.photo_path }}" class="profile-user-img img-responsive img-circle" alt="User Image">
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
      var customer = new Vue({
        el: '#customer',

        data: {
          users: []
        },

        mounted: function() {
          console.log("inside ready");
          var pusher = new Pusher('f4976d40a137b96b52ea', {
            encrypted: true
          });

          pusher.subscribe("{!! 'business' . $profile->id !!}")
            .bind('App\\Events\\CustomerEnterRadius', this.addUser);
        },

        methods: {
          addUser: function(user) {
            var activeCustomer = user.user;
            this.users.push(activeCustomer);
          }
        }
      })


    </script>
@stop






