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
<div class="content-wrapper-scroll" id="customer">
  <div class="scroll-main">
    <div class="scroll-main-contents">
      <section class="content-header">
        <h1>
          Customer Dashboard
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        </ol>
      </section>
      <section class="content">
        <form class="customer-search">
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
            <input style="font-size: 28px; padding: 0px;" type="text" name="query" class="form-control" placeholder="Search" v-model="query">
          </div>
        </form>
        <div class="invite-code-section">
          <button type="button" class="btn btn-warning btn-flat" v-if="!inviteCodeGenerated" v-on:click="createInviteCode()" style="padding: 8px;">New Invite Code</button>
          <h4 class="invite-code" v-if="inviteCodeGenerated">Single use invite code: <strong style="color:#000000;">@{{ inviteCodeGenerated }}</strong></h4>
          <a class="invite-code-hide" href="#" v-if="inviteCodeGenerated" v-on:click="inviteCodeGenerated = null">Hide</a>
        </div>
        <div class="scroll-container">
          <div class="scroll-contents">
            <template v-for="user in customerFilter">
              <div class="col-sm-4 col-md-3">
                <div class="box box-primary">
                  <div class="box-header with-border text-center">
                    <a v-on:click="getCustomerData(user)" class="customer-name-title" href="#">
                      <h3 class="box-title">@{{user.first_name}} @{{user.last_name}}</h3>
                    </a>
                    <div class="box-body">
                      <a v-on:click="getCustomerData(user)"  href="#">
                        <img v-if="user.photo_path" :src="user.photo_path" class="profile-user-img img-responsive img-circle" alt="User Image">
                        <img v-else src="{{ asset('/images/icon-profile-photo.png') }}" class="profile-user-img img-responsive img-circle">
                      </a>
                    </div>
                    <div class="box-footer">
                      @if($profile->tip_tracking_enabled)
                        <a href="#" v-on:click="openEmployeeModal(user.id)" class="btn btn-primary btn-block">
                          <b>Bill</b>
                        </a>
                      @else
                        <a href="#" v-on:click="goToTransaction(user.id)" class="btn btn-primary btn-block">
                          <b>Bill</b>
                        </a>
                      @endif
                      <div v-if="checkForDeal(user.id)">
                        <a href="#" data-toggle="modal" data-target="#redeemDealModal" class="btn btn-success btn-block btn-redeem">
                          <b>Redeem Deal</b>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
      </section>
      <form><input type="hidden" name="_token" value="{{ csrf_token() }}"></form>
    </div>
  </div>
  <div class="modal fade" id="redeemDealModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header-timeline">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="redeemDealModal">Purchased Deals | @{{selectedUser.first_name}} @{{selectedUser.last_name}}</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <div v-for="deal in deals">
              <div v-if="deal.user_id === selectedUser.id">
                <span class="pull-left">
                  <h3 class="deal-item">@{{ deal.products }}</h3>
                </span>
                <span class="pull-right">
                  <button v-on:click="RedeemDeal(deal.id)" data-dismiss="modal" class="btn btn-block btn-success pull-right">Redeem!</button>
                </span>
              </div>
            </div>
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
          <h3 class="modal-title" id="CustomerinfoModal">Info | @{{selectedUser.first_name}} @{{selectedUser.last_name}}</h3>
        </div>
        <div class="modal-body-customer-info">
          <section class="content">
            <div class="row">
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="info-box">
                  <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-text">Date Last Purchase</span>
                    <span v-if="purchases.length == 0" class="info-box-number">No Purchases</span>
                    <span v-if="purchases.length != 0" class="info-box-number">@{{ lastPurchase.updated_at | setDateTime }}</span>
                  </div>
                </div>
                <div v-if="purchases.length != 0" class="box box-aqua collapsed-box">
                  <div class="box-header with-border">
                    <i class="fa fa-shopping-cart"></i>
                    <h3 class="box-title">View Purchases</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                      </button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div v-for="item in lastItemsPurchased">
                      <p class="timeline-purchases-left">@{{ item.quantity }} x @{{ item.name }}</p>
                      <p class="timeline-purchases-right">$@{{ (item.price / 100) }}</p>
                    </div>
                    <div class="box-footer timeline-list-footer">
                      <div class="last-purchase-footer pull-right">Tax: $@{{ lastPurchase.tax / 100 }}</div>
                      <div class="last-purchase-footer pull-right" style="margin-bottom: 10px;">Tip: $@{{ lastPurchase.tips / 100 }}</div>
                      <div class="last-purchase-footer pull-right"><b>Total: $@{{ lastPurchase.total / 100 }}</b></div>
                    </div>
                  </div>
                </div>
                <div class="info-box">
                  <span class="info-box-icon bg-yellow"><i class="fa fa-bookmark-o"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-text">Recent Bookmarked</span>
                    <span v-if="!recentBookmarked" class="info-box-number">No Recent</span>
                    <span v-if="recentBookmarked" class="info-box-number">@{{ recentBookmarked.bookmarked_on | setDateTime }}</span>
                  </div>
                </div>
                <div v-if="recentBookmarked" class="box box-warning collapsed-box">
                  <div class="box-header with-border">
                    <i class="fa fa-bookmark-o"></i>
                    <h3 class="box-title">Bookmark Details</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                      </button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="analytics-modal-image">
                      <img v-if="recentBookmarked.thumb_path" :src="recentBookmarked.thumb_path">
                    </div>
                    <div class="box-body-bottom">
                      <h4 v-if="recentBookmarked.message" class="box-title customer-data-message">@{{ recentBookmarked.message }}</h4>
                      <h4 v-else="!recentBookmarked.message" class="box-title customer-data-message">@{{ recentBookmarked.title }}</h4>
                    </div>
                    <hr style="margin-top: 10px; margin-bottom: 10px;">
                    <p class="analytics-date-customer-data">Posted on <strong>@{{ recentBookmarked.published_at | setDateTime }}</strong>.</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="info-box">
                  <span class="info-box-icon bg-green"><i class="fa fa-eye"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-text">Last Viewed Post</span>
                    <span v-if="!lastViewedPost" class="info-box-number">No Recent</span>
                    <span v-if="lastViewedPost" class="info-box-number">@{{ lastViewedPost.updated_at | setDateTime }}</span>
                  </div>
                </div>
                <div v-if="lastViewedPost" class="box box-success collapsed-box">
                  <div class="box-header with-border">
                    <i class="fa fa-eye"></i>
                    <h3 class="box-title">Post Details</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                      </button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="analytics-modal-image">
                      <img v-if="lastViewedPost.thumb_path" :src="lastViewedPost.thumb_path">
                    </div>
                    <div class="box-body-bottom">
                      <h4 v-if="lastViewedPost.message" class="box-title customer-data-message">@{{ lastViewedPost.message }}</h4>
                      <h4 v-else="!lastViewedPost.message" class="box-title customer-data-message">@{{ lastViewedPost.title }}</h4>
                    </div>
                    <hr style="margin-top: 10px; margin-bottom: 10px;">
                    <p class="analytics-date-customer-data">Posted on <strong>@{{ lastViewedPost.published_at | setDateTime }}</strong>.</p>
                  </div>
                </div>
                <div class="info-box">
                  <span class="info-box-icon bg-red"><i class="fa fa-share"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-text">Recent Shared</span>
                    <span v-if="!recentShared" class="info-box-number">No Recent</span>
                    <span v-if="recentShared" class="info-box-number">@{{ recentShared.shared_on | setDateTime }}</span>
                  </div>
                </div>
                <div v-if="recentShared" class="box box-danger collapsed-box">
                  <div class="box-header with-border">
                    <i class="fa fa-share"></i>
                    <h3 class="box-title">Shared Post Details</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                      </button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="analytics-modal-image">
                      <img v-if="recentShared.thumb_path" :src="recentShared.thumb_path">
                    </div>
                    <div class="box-body-bottom">
                      <h4 v-if="recentShared.message" class="box-title customer-data-message">@{{ recentShared.message }}</h4>
                      <h4 v-else="!recentShared.message" class="box-title customer-data-message">@{{ recentShared.title }}</h4>
                    </div>
                    <hr style="margin-top: 10px; margin-bottom: 10px;">
                    <p class="analytics-date-customer-data">Posted on <strong>@{{ recentShared.published_at | setDateTime }}</strong>.</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div v-show="purchases.length != 0" class="col-md-12">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <i class="fa fa-history"></i>
                    <h3 class="box-title">Date of Last 5 Purchases</h3>
                  </div>
                  <div class="box-body">
                    <div class="chart">
                      <canvas id="purchaseHistory" width="400" height="150"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="EmployeeChooseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header-timeline">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="EmployeeChooseModal">Please choose Team Member</h4>
        </div>
        <div class="modal-body-analytics">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box box-success">
              <div class="box-header with-border">
                <h3 class="box-title">On Shift</h3>
                <div class="box-tools pull-right"><span class="label label-success">@{{ employees.length }} on</span></div>
              </div>
              <div v-if="employees.length > 0" class="box-body no-padding">
                <ul class="users-list clearfix">
                  <li v-for="employee in employees" v-on:click="setEmployee(employee.id)">
                    <img v-if="employee.photo_path" :src="employee.photo_path" style="max-height: 75px;" alt="Employee Image">
                    <img v-else src="{{ asset('/images/icon-profile-photo.png') }}" style="max-height: 75px;" alt="User Image">
                    <a class="users-list-name" href="#" v-on:click="toggleShift(employee.id)">@{{ employee.first_name }} @{{ employee.last_name }}</a>
                  </li>
                </ul>
              </div>
              <div v-else class="box-body">
                <h4>You are currently using Tip Tracking.At least one Team Member must be clocked-in.</h4>
                <h5>Please clock-in in the Team tab.</h5>
                <a href="{{ route('employees.show') }}">
                  <button class="btn btn-primary pull-right">Go to Team</button>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
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
          purchases: [],
          deals: [],
          lastPurchase: {},
          lastViewedPost: null,
          recentBookmarked: null,
          recentShared: null,
          lastItemsPurchased: [],
          inviteCodeGenerated: null,
          query: '',
          selectedUser: {},
          employees:[],
          customerIdBill: '',
          selectedEmployeeId: ''
        },

        mounted: function() {

          var pusher = new Pusher('f4976d40a137b96b52ea', {
            encrypted: true
          });

          pusher.subscribe("{!! 'business' . $profile->id !!}")
            .bind('App\\Events\\CustomerEnterRadius', this.addUser);

          pusher.subscribe("{!! 'remove' . $profile->id !!}")
            .bind('App\\Events\\CustomerLeaveRadius', this.removeUser);

          window.setInterval(this.removeInactiveUser, 300000);

          this.getCustomersInLocation();
        },

        filters: {
          setDate: function(value) {
            date = moment(value).format("Do MMM YY");
            return date;
          },
          setDateTime: function(value) {
            date = moment(value).format("Do MMM YY [at] h:mm a");
            return date;
          },
        },

        computed: {
          customerFilter: function() {
            return this.findBy(this.users, this.query, 'first_name', 'last_name');
          }
        },

        methods: {
          findBy: function(list, value, column_first, column_last) {
            return list.filter(function(customer) {
              return (customer[column_first].toLowerCase().includes(value.toLowerCase()) || customer[column_last].toLowerCase().includes(value.toLowerCase()));
            });
          },

          createInviteCode: function() {
            var businessId = '{{ $profile->id }}'

            $.ajax({
              method: 'POST',
              url: '/invites/business/new',
              data: {
                'businessId' : businessId
              },
              success: function(data) {
                customer.$data.inviteCodeGenerated = data;
              },
              error: function(data) {
                console.log(data);
              }
            })
          },

          getCustomersInLocation: function() {
            var businessId = '{{ $profile->id }}'
            $.ajax({
              method: 'POST',
              url: '/geo/location/users',
              data: {
                'businessId' : businessId
              },
              success: function(data) {
                if (data != 'none') {
                  data.forEach(function(user) {
                    customer.addUser(user);
                  })
                }
              },
              error: function(data) {
                console.log(data);
              }
            })
          },

          addUser: function(data) {
            if (data.user) {
              var activeCustomer = data.user;
            } else {
              var activeCustomer = data;
            }
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
            customer.getRedeemableDeals(activeCustomer.id);
          },
          removeUser: function(data) {
            console.log("remove user by distance");
            console.log(data);
            var leavingCustomer = data.user;
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
                if (currentTime - userLastActive >= 600000) {
                  var businessId = '{{ $profile->id }}'
                  this.deleteInactiveUser(users[i].id, businessId);
                  users.splice(i, 1);
                }
              }
            }
          },
          openEmployeeModal: function(customerId) {
            var businessId = '{{ $profile->id }}';
            this.customerIdBill = customerId;
            $.ajax({
              method: 'POST',
              url: '/employees/on',
              data: {
                'businessId': businessId,
                'customerId': customerId
              },
              success: function(data) {
                if (data.employeesOn) {
                  customer.$data.employees = data.employeesOn;
                  if (data.employeesOn.length == 1) {
                    customer.$data.selectedEmployeeId = data.employeesOn[0].id;
                    return customer.goToTransaction(customerId);
                  } else {
                    $('#EmployeeChooseModal').modal('show');
                  }
                } else {
                  customer.$data.selectedEmployeeId = data.billOpen.employeeId;
                  return customer.goToTransaction(customerId);
                }
              }
            })
          },
          setEmployee: function(employeeId) {
            this.selectedEmployeeId = employeeId;
            $('#EmployeeChooseModal').modal('hide');
            return this.goToTransaction(this.customerIdBill);
          },
          goToTransaction: function(customerId) {
            if (this.selectedEmployeeId != '') {
              employeeId = this.selectedEmployeeId;
            } else {
              employeeId = 'empty';
            }
            route = "{{ route('bill.show', ['customerId' => 'id', 'employeeId' => 'eId']) }}"
            route = route.replace('id', customerId);
            location.href = route.replace('eId', employeeId);
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
          getCustomerData: function(user) {
            var businessId = '{{ $profile->id }}'

            $.ajax({
              method: 'POST',
              url: '/user/purchases',
              data: {
                'customerId' : user.id,
                'businessId' : businessId
              },
              success: function(data) {
                var dataStorage = customer.$data;
                dataStorage.selectedUser = user;
                dataStorage.purchases = data.purchases;
                dataStorage.lastViewedPost = data.lastViewedPost;
                dataStorage.recentBookmarked = data.recentBookmarkedPost;
                dataStorage.recentShared = data.recentSharedPost;
                if (dataStorage.purchases.length !== 0 ) {
                  dataStorage.lastPurchase = dataStorage.purchases[0];
                  dataStorage.lastItemsPurchased = JSON.parse(dataStorage.lastPurchase.products);
                  $('#CustomerinfoModal').modal('show');
                  customer.drawChart();
                } else {
                  dataStorage.lastPurchase = null;
                  dataStorage.lastItemsPurchased = null;
                }
              },
              error: function(data) {
                console.log(data);
              }
            });
          },
          drawChart: function() {
            console.log("hello");
            var dataStorage = customer.$data;
            var purchaseHistoryCanvas = $("#purchaseHistory").get(0).getContext("2d");
            var purchasesData = [];
            dataStorage.purchases.forEach(function(purchase) {
              var point = {x: purchase.updated_at, y: 0};
              purchasesData.push(point);
            });
            var today = {x: moment().format(), y: 0};
            purchasesData.push(today);
            var purchaseHistoryChart = new Chart(purchaseHistoryCanvas, {
              type: 'line',
              data: {
                datasets: [{
                  data: purchasesData,
                  pointRadius: 5,
                  borderColor: "rgba(52, 152, 219,1.0)",
                  pointBorderColor: ["#f39c12", "#f39c12", "#f39c12", "#f39c12", "#f39c12", "#00a65a"],
                  pointBackgroundColor: ["#f39c12", "#f39c12", "#f39c12", "#f39c12", "#f39c12", "#00a65a"],
                }]
              },
              options: {
                responsive: true,
                legend: {
                  display: false
                },
                tooltips: {
                  titleFontSize: 0,
                  callbacks: {
                    label: function(tooltipItem) {
                      var time = moment(tooltipItem.xLabel).format('MMM Do YY');
                      if (time == moment().format('MMM Do YY')) {
                        return "Today"
                      } else {
                        return time;
                      }
                    }
                  }
                },
                scales: {
                  yAxes:[{
                    display: false,
                  }],
                  xAxes: [{
                    ticks: {
                      callback: function(value, index, values) {
                        var formattedTick = (moment(value, 'MMM DD, YYYY').format('MMM D YY'));
                        var checkDate = null;
                        purchasesData.forEach(function(date) {
                          var formattedDate = moment(date.x).format('MMM D YY');
                          if (formattedTick == formattedDate) {
                            checkDate = value;
                            if (formattedDate == moment().format('MMM D YY')) {
                              checkDate = "Today";
                            }
                          }
                        });
                        return checkDate;
                      }
                    },
                    gridLines: {
                      display: false,
                      drawBorder: false,
                    },
                    type: 'time',
                    time: {
                      unit: 'day',
                      displayFormats: {
                        day: 'll'
                      }
                    }
                  }]
                }
              }
            });
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
              success: function(data) {
                console.log(data);
                var dataStorage = customer.$data;
                var deals = dataStorage.deals;
                if (data.redeemableDeals.length > 0 ) {
                  data.redeemableDeals.forEach(function (userDeal) {
                    var found = false;
                    if (deals.length > 0) {
                      deals.forEach(function(currentUserDeals) {
                        if (currentUserDeals.id === userDeal.id) {
                          found = true;
                        }
                      }); 
                      if (found === false) {
                        data.posts.forEach(function(post) {
                          if (post.id === userDeal.deal_id) {
                            userDeal.products = post.deal_item;
                          }
                        });
                        deals.push(userDeal)
                      }
                    } else {
                      data.posts.forEach(function(post) {
                        if (post.id === userDeal.deal_id) {
                          userDeal.products = post.deal_item;
                        }
                      });
                      deals.push(userDeal)
                    }
                  });
                }
              } 
            })
          },
          checkForDeal(userId) {
            var dataStorage = customer.$data;
            if (dataStorage.deals.length > 0 ) {
              var found = false;
              dataStorage.deals.forEach(function(e) {
                if (e.user_id === userId) {
                  found = true;
                }
              });
              return found;
            }
          },
          RedeemDeal(dealId) {
            $.ajax({
              method: 'POST',
              url: '/user/deal/redeem',
              data: {
                'dealId' : dealId,
              },
              success: function(data) {
                var dataStorage = customer.$data;
                var deals = dataStorage.deals;
                for (i=deals.length - 1; i >= 0; i --) {
                  if (deals[i].id === dealId) {
                    deals.splice(i, 1);
                  }
                }
               toastr["success"]("Deal Redeemed!", "Success", {
                  "newestOnTop": true,
                  "timeOut": 5000,
                  "extendedTimeOut": 5000,
                })
              }
            })
          }
        }
      })
    </script>
@stop






