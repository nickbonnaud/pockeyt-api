<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta property="og:site_name" content="Pockeyt" />
    <title>Pockeyt Business</title>
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/AdminLTE.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/skin-yellow.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/jqueryui/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/sweetalert/dist/sweetalert.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.css">
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2-rc.1/css/select2.min.css" />
</head>

<body class="hold-transition skin-yellow sidebar-mini">
    <div class="wrapper" id="wrapper">

      <header class="main-header">
        <!-- Logo -->
        <a href="{{ route('profiles.show', ['profiles' => $user->profile->id]) }}" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini">
            <img src="{{ asset('/images/white-logo.png') }}">
          </span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg">
            <img src="{{ asset('/images/logo-horizontal-white.png') }}">
          </span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>

          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    @if(is_null($user->photo_path))
                        <img src="{{ asset('/images/icon-profile-photo.png') }}" class="user-image" alt="User Image">
                    @else
                        <img src="{{ $user->photo_path }}" class="user-image" alt="User Image">
                    @endif
                  <span class="hidden-xs">{{ $user->first_name }}</span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    @if(is_null($user->photo_path))
                        <img src="{{ asset('/images/icon-profile-photo.png') }}" class="img-circle" alt="User Image">
                    @else
                        <img src="{{ $user->photo_path }}" class="img-circle" alt="User Image">
                    @endif
                    <p>
                      {{ $user->first_name }} {{ $user->last_name }}
                    </p>
                  </li>
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="{{ route('users.show', ['users' => $user->id])  }}" class="btn btn-default btn-flat">User Profile</a>
                    </div>
                    <div class="pull-right">
                      <a href="{{ route('auth.logout') }}" class="btn btn-default btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
              <!-- Control Sidebar Toggle Button -->
              <li>
                <a href="#" data-toggle="control-sidebar" v-on:click="loadTransactions()"><i class="fa fa-gears"></i></a>
              </li>
            </ul>
          </div>
        </nav>
      </header>

      <!-- =============================================== -->

      <!-- Left side column. contains the sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
                @if(is_null($user->profile->logo))
                    <img src="{{ asset('/images/icon-profile-photo.png') }}" class="img-circle" alt="Profile Image">
                @else
                    <img src="{{ $user->profile->logo->url }}" class="img-circle" alt="Profile Image">
                @endif
            </div>
            <div class="pull-left info profile-status">
                <p>{{ $user->profile->business_name }}</p>
                @if($user->profile->approved)
                    <span><i class="fa fa-circle text-success"></i> Profile Approved</span>
                @else
                    <span href="#"><i class="fa fa-circle text-danger"></i> Profile Waiting Approval</span>
                @endif
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-building"></i>
                <span>Your Business Info</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="{{ route('profiles.edit', ['profiles' => $user->profile->id])  }}"><i class="fa fa-circle-o"></i> Profile Info</a></li>
                <li><a href="{{ route('accounts.edit', ['account' => $user->profile->account->id]) }}"><i class="fa fa-circle-o"></i> Payment Account Info</a></li>
              </ul>
            </li>
            <li><a href="{{ route('posts.list') }}"><i class="fa fa-rss"></i> <span>Posts</span></a></li>
            <li><a href="{{ route('posts.events') }}"><i class="fa fa-calendar"></i> <span>Events</span></a></li>
            <li><a href="{{ route('products.list') }}"><i class="fa fa-shopping-cart"></i> <span>Inventory</span></a></li>
            <li><a href="{{ route('loyalty-programs.create') }}"><i class="fa fa-trophy"></i> <span>Loyalty Program</span></a></li>
            <li><a href="{{ route('posts.deals') }}"><i class="fa fa-bolt"></i> <span>Deals</span></a></li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>

  @yield('content')
  
      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
          <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>

          <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
          <!-- Home tab content -->
          <div class="tab-pane" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading">Recent Transactions</h3>
            <ul class="control-sidebar-menu">
              <li v-for="transaction in transactions">
                <a v-if="transaction.status === 0" href="javascript:void(0)">
                  <i class="menu-icon fa fa-paper-plane-o bg-yellow"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">@{{ transaction.customerName }}</h4>

                    <p>Failed to send Bill to Customer</p>
                  </div>
                </a>
                <a v-else-if="transaction.status === 1" href="javascript:void(0)">
                  <i class="menu-icon fa fa-paper-plane-o bg-yellow"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">@{{ transaction.customerName }}</h4>

                    <p>Unable to charge Card</p>
                  </div>
                </a>
                 <a v-else-if="transaction.status === 10" href="javascript:void(0)">
                  <i class="menu-icon fa fa-paper-plane-o bg-yellow"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">@{{ transaction.customerName }}</h4>

                    <p>Bill Sent to Customer</p>
                  </div>
                </a>
                <a v-else-if="transaction.status === 11" href="javascript:void(0)">
                  <i class="menu-icon fa fa-thumbs-o-up bg-light-blue"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">@{{ transaction.customerName }}</h4>

                    <p>Waiting for Customer to approve bill.</p>
                  </div>
                </a>
                <a v-else-if="transaction.status === 20" href="javascript:void(0)">
                  <i class="menu-icon fa fa-smile-o bg-green"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">@{{ transaction.customerName }}</h4>

                    <p>Paid!</p>
                  </div>
                </a>
              </li>
            </ul>
            <!-- /.control-sidebar-menu -->
          </div>
          <!-- /.tab-pane -->
          <!-- Stats tab content -->
          <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
          <!-- /.tab-pane -->
          <!-- Settings tab content -->
          <div class="tab-pane" id="control-sidebar-settings-tab">
            <form method="post">
              <h3 class="control-sidebar-heading">General Settings</h3>

              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Report panel usage
                  <input type="checkbox" class="pull-right" checked>
                </label>

                <p>
                  Some information about this general settings option
                </p>
              </div>
              <!-- /.form-group -->

              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Allow mail redirect
                  <input type="checkbox" class="pull-right" checked>
                </label>

                <p>
                  Other sets of options are available
                </p>
              </div>
              <!-- /.form-group -->

              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Expose author name in posts
                  <input type="checkbox" class="pull-right" checked>
                </label>

                <p>
                  Allow the user to show his name in blog posts
                </p>
              </div>
              <!-- /.form-group -->

              <h3 class="control-sidebar-heading">Chat Settings</h3>

              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Show me as online
                  <input type="checkbox" class="pull-right" checked>
                </label>
              </div>
              <!-- /.form-group -->

              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Turn off notifications
                  <input type="checkbox" class="pull-right">
                </label>
              </div>
              <!-- /.form-group -->

              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Delete chat history
                  <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
                </label>
              </div>
              <!-- /.form-group -->
            </form>
          </div>
          <!-- /.tab-pane -->
        </div>
      </aside>
      <!-- /.control-sidebar -->
      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

	<script src="{{ asset('/vendor/jquery/jquery-2.2.3.min.js') }}"></script>
	<script src="{{ asset('/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('/vendor/slimScroll/jquery.slimscroll.min.js') }}"></script>
	<script src="{{ asset('/vendor/fastclick/fastclick.js') }}"></script>
	<script src="{{ asset('/js/app.min.js') }}"></script>
	<script src="{{ asset('/vendor/jqueryui/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('/vendor/vue/vue.min.js') }}"></script>
  <script src="{{ asset('/vendor/sweetalert/dist/sweetalert.min.js') }}"></script>
  <script src="{{ asset('/vendor/moment/min/moment.min.js') }}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2-rc.1/js/select2.min.js"></script>
	@yield('scripts.footer')
  @include('flash')
  <script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var wrapper = new Vue({
      el: '#wrapper',

      data: {
        transactions: []
      },

      methods: {

        loadTransactions: function() {
          console.log("init");
          var businessId = '{{ $user.profile.id }}';
          $.ajax({
            method: 'POST',
            url: '/business/transactions',
            data: {
              'businessId' : businessId
            },
            success: data => {
              console.log(data);
            },
            error: err => {
              console.log(err);
            }
          })
        }
      }
    })



  </script>
</body>
</html>