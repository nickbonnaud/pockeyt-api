<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <script src="{{ asset('/vendor/standAlone/stand-alone.js') }}"></script>
  <meta name="apple-mobile-web-app-title" content="Pockeyt Business">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta property="og:site_name" content="Pockeyt" />
  <title>Pockeyt Business</title>
  <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/AdminLTE.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/skin-yellow.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/jqueryui/css/jquery-ui.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/sweetalert/dist/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/toastr/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/select2/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/pace/pace-theme-minimal.css') }}">
  <link rel="manifest" href="/manifest.json">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.css">
  <link rel="shortcut icon" href="/images/favicon.ico" type="image/icon" />
  <link rel="apple-touch-startup-image" href="/images/launch.png">
</head>

<body class="hold-transition skin-yellow sidebar-mini">
    <div class="wrapper">

      <header class="main-header">
        <!-- Logo -->
        <a href="{{ route('profiles.show', ['profiles' => Crypt::encrypt($user->profile->id)]) }}" class="logo">
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
                      <a href="{{ route('users.show', ['users' => Crypt::encrypt($user->id)])  }}" class="btn btn-default btn-flat">User Profile</a>
                    </div>
                    <div class="pull-right">
                      <a href="{{ route('auth.logout') }}" class="btn btn-default btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
              <!-- Control Sidebar Toggle Button -->
              <li>
                <a href="#" data-toggle="control-sidebar"><i class="fa fa-check-square-o"></i></a>
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
            <li><a href="{{ route('profiles.show', ['profiles' => Crypt::encrypt($user->profile->id)]) }}"><i class="fa fa-dashboard"></i> <span class="menu-text">Dashboard</span></a></li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-building"></i>
                <span class="menu-text">Your Business Info</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="{{ route('profiles.edit', ['profiles' => Crypt::encrypt($user->profile->id)])  }}"><i class="fa fa-circle-o"></i> Profile Info</a></li>
                @if(!$user->profile->account)
                  <li><a href="{{ route('accounts.create') }}"><i class="fa fa-circle-o"></i> Create Payment Account</a></li>
                @elseif($user->profile->account->accountEmail && !$user->profile->account->ssn)
                  <li><a href="{{ route('accounts.createOwner') }}"><i class="fa fa-circle-o"></i> Finish Payment Account</a></li>
                @elseif($user->profile->account->ssn && !$user->profile->account->routing)
                  <li><a href="{{ route('accounts.createBank') }}"><i class="fa fa-circle-o"></i> Finish Payment Account</a></li>
                @elseif($user->profile->account->routing)
                  <li><a href="{{ route('accounts.edit', ['account' => Crypt::encrypt($user->profile->account->id)]) }}"><i class="fa fa-circle-o"></i> Payment Account Info</a></li>
                @endif
                  <li><a href="{{ route('accounts.connections') }}"><i class="fa fa-circle-o"></i> Account Connections</a></li>
              </ul>
            </li>
            <li><a href="{{ route('posts.list') }}"><i class="fa fa-rss"></i> <span class="menu-text">Posts</span></a></li>
            <li><a href="{{ route('posts.events') }}"><i class="fa fa-calendar"></i> <span class="menu-text">Events</span></a></li>
            <li><a href="{{ route('products.list', ['profiles' => Crypt::encrypt($user->profile->id)]) }}"><i class="fa fa-shopping-cart"></i> <span class="menu-text">Inventory</span></a></li>
            <li><a href="{{ route('loyalty-programs.create') }}"><i class="fa fa-trophy"></i> <span class="menu-text">Loyalty Program</span></a></li>
            <li><a href="{{ route('posts.deals') }}"><i class="fa fa-bolt"></i> <span class="menu-text">Deals</span></a></li>
            <li><a href="{{ route('analytics.show') }}"><i class="fa fa-line-chart"></i> <span class="menu-text">Analytics Dashboard</span></a></li>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-archive"></i>
                <span class="menu-text">Sales Center</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="{{ route('sales.show') }}"><i class="fa fa-circle-o"></i> Sales Breakdown</a></li>
                @if($user->profile->tip_tracking_enabled)
                  <li><a href="{{ route('employees.show') }}"><i class="fa fa-circle-o"></i> Team</a></li>
                @endif
                <li><a href="{{ route('transactions.refund') }}"><i class="fa fa-circle-o"></i> Issue Refund</a></li>
              </ul>
            </li>
            @if($user->is_admin)
              <li><a href="{{ route('businesses.review') }}"><i class="fa fa-key"></i> <span class="menu-text">Pending Businesses</span></a></li>
            @endif
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>

  @yield('content')
  
      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
          <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-spinner"></i></a></li>
          <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-check"></i></a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content" id="tab">
          <!-- Home tab content -->
          <div class="tab-pane active" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading" v-if="transactionsPending.length != 0">Pending Transactions</h3>
            <h3 class="control-sidebar-heading" v-else>No Pending Transactions</h3>
            <ul class="control-sidebar-menu">
              <li v-for="transaction in transactionsPending">
                <a href="javascript:void(0)" v-if="transaction.status === 0">
                  <i class="menu-icon fa fa-warning bg-red"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">@{{ transaction.customerName }}</h4>

                    <p>Failed to send Bill to customer</p>
                  </div>
                </a>
                <a href="javascript:void(0)" v-if="transaction.status === 1">
                  <i class="menu-icon fa fa-warning bg-red"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">@{{ transaction.customerName }}</h4>

                    <p>Unable to charge Card</p>
                  </div>
                </a>
                <a href="javascript:void(0)" v-if="transaction.status === 2">
                  <i class="menu-icon fa fa-warning bg-red"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">@{{ transaction.customerName }}</h4>

                    <p>Bill declined by customer</p>
                  </div>
                </a>
                <a href="javascript:void(0)" v-if="transaction.status === 10">
                  <i class="menu-icon fa fa-paper-plane-o bg-yellow"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">@{{ transaction.customerName }}</h4>

                    <p>Bill sent to customer</p>
                  </div>
                </a>
                <a href="javascript:void(0)" v-if="transaction.status === 11">
                  <i class="menu-icon fa fa-thumbs-o-up bg-light-blue"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">@{{ transaction.customerName }}</h4>

                    <p>Waiting customer approval</p>
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
            <div v-if="transactionsFinalized.length != 0">
              <h3 class="control-sidebar-heading">Recent transactions</h3>
              <ul class="control-sidebar-menu">
                <li v-for="transaction in transactionsFinalized">
                  <a href="javascript:void(0)" v-if="transaction.status === 20">
                    <i class="menu-icon fa fa-star-o bg-green"></i>

                    <div class="menu-info">
                      <h4 class="control-sidebar-subheading">@{{ transaction.customerName }}</h4>

                      <p>Paid!</p>
                    </div>
                  </a>
                </li>
              </ul>
            </div>
            <div v-else>
              <h3 class="control-sidebar-heading">No recent transactions</h3>
            </div>
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
  <script src="{{ asset('/vendor/pace/pace.min.js') }}"></script>
	<script src="{{ asset('/vendor/jquery/jquery-2.2.3.min.js') }}"></script>
	<script src="{{ asset('/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('/vendor/slimScroll/jquery.slimscroll.min.js') }}"></script>
	<script src="{{ asset('/vendor/fastclick/fastclick.js') }}"></script>
	<script src="{{ asset('/js/app.min.js') }}"></script>
	<script src="{{ asset('/vendor/jqueryui/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('/vendor/vue/vue.min.js') }}"></script>
  <script src="{{ asset('/vendor/sweetalert/dist/sweetalert.min.js') }}"></script>
  <script src="{{ asset('/vendor/moment/min/moment.min.js') }}"></script>
  <script src="{{ asset('/vendor/toastr/toastr.min.js') }}"></script>
  <script src="{{ asset('/vendor/chart.js/dist/Chart.min.js') }}"></script>
  <script src="{{ asset('/vendor/daterangepicker/daterangepicker.js') }}"></script>
  <script src="{{ asset('/vendor/vMask/v-mask.min.js') }}"></script>
  <script src="{{ asset('/vendor/vMask/v-money.js') }}"></script>
	<script src="{{ asset('/vendor/select2/select2.min.js') }}"></script>
  <script src="{{ asset('/vendor/noBounce/inobounce.min.js') }}"></script>
  <script src="//js.pusher.com/3.2/pusher.min.js"></script>
	@yield('scripts.footer')
  @include('flash')
  <style>
    html { display:none; }
  </style>
  <script>

    if (self == top) { 
      document.documentElement.style.display = 'block'; 
    } else {
      top.location = self.location;
    };

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var tab = new Vue({
      el: '#tab',

      data: {
        transactionsPending: [],
        transactionsFinalized: []
      },

      mounted: function() {
        var pusher = new Pusher('f4976d40a137b96b52ea', {
          encrypted: true
        });
        pusher.subscribe("{!! 'reward' . $user->profile->id !!}")
          .bind('App\\Events\\RewardNotification', this.notifyReward);

        pusher.subscribe("{!! 'transaction' . $user->profile->id !!}")
          .bind('App\\Events\\TransactionsChange', this.loadTransactions);

        pusher.subscribe("{!! 'error' . $user->profile->id !!}")
          .bind('App\\Events\\ErrorNotification', this.notifyError);

        pusher.subscribe("{!! 'billRequest' . $user->profile->id !!}")
          .bind('App\\Events\\CustomerRequestBill', this.notifyBill);

        pusher.subscribe("{!! 'paymentSuccess' . $user->profile->id !!}")
          .bind('App\\Events\\PaymentSuccessNotification', this.notifyPaymentSuccess);

        window.setInterval(this.checkSession, 1920000);
      },

      methods: {
        notifyPaymentSuccess: function(data) {
          toastr["success"]("Payment Received!<br /><br /><button type='button' class='btn btn-default'>Ok</button>", data.user.first_name + " " + data.user.last_name + " has paid their bill.", {
            "newestOnTop": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
          })
        },

        notifyReward: function(data) {
          toastr["info"]("Loyalty reward earned!<br /><br /><button type='button' class='btn btn-default'>Ok</button>", data.user.first_name + " " + data.user.last_name + " has earned: " + data.loyaltyProgram.reward, {
            "newestOnTop": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
          })
        },

        notifyBill: function(data) {
          toastr["info"](data.user.first_name + " " + data.user.last_name + " has requested their bill.<br /><br /><button type='button' class='btn btn-default'>Send Bill</button>", "Bill Requested!", {
            "newestOnTop": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
            "onclick": function() {
              var employeeId = 'empty';
              route = "{{ route('bill.show', ['customerId' => 'id', 'employeeId' => 'eId']) }}"
              route = route.replace('id', data.user.id)
              location.href = route.replace('eId', employeeId);
            }
          })
        },

        notifyError: function(data) {
          if (data.transaction.status === 1) {
            toastr["error"]("Charge Failed<br /><br /><button type='button' class='btn btn-default'>Ok</button>", "Unable to charge " + data.user.first_name + " " + data.user.last_name + ". Unable to process payment for transaction id: " + data.transaction.id + ". Please contact Customer Support.", {
              "newestOnTop": true,
              "timeOut": 0,
              "extendedTimeOut": 0,
            })
          } else if (data.transaction.status === 2) {
            toastr["error"]("Bill Declined<br /><br /><button type='button' class='btn btn-default'>Ok</button>", data.user.first_name + " " + data.user.last_name + " declined the bill. Please check with " + data.user.first_name + " to settle dispute and re-submit the bill.", {
              "newestOnTop": true,
              "timeOut": 0,
              "extendedTimeOut": 0,
            })
          }
        },

        loadTransactions: function() {
          var businessId = '{{ $user->profile->id }}';
          $.ajax({
            method: 'POST',
            url: '/business/transactions',
            data: {
              'businessId' : businessId
            },
            success: function(data) {
              tab.$data.transactionsPending = data.transactionsPending;
              tab.$data.transactionsFinalized = data.transactionsFinalized;
            },
            error: function(data) {
              console.log(data);
            }
          })
        },

        checkSession: function() {
          $.ajax({
            method: 'GET',
            url: '/auth/session',
            success: function(data) {
              if (data.guest) {
                window.location.replace('https://pockeytbiz.com');
              }
            },
            error: function(data) {
              console.log(data);
              window.location.replace('https://pockeytbiz.com');
            }
          })
        }
      }
    });
    tab.loadTransactions();
  </script>
</body>
</html>