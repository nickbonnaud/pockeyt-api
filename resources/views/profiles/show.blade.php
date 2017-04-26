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
    <div class="invite-code-section">
      <button type="button" class="btn btn-warning btn-flat" v-if="!inviteCodeGenerated" v-on:click="createInviteCode()">New Invite Code</button>
      <h4 class="invite-code" v-if="inviteCodeGenerated">Single use invite code: <strong style="color:#000000;">@{{ inviteCodeGenerated }}</strong></h4>
      <a class="invite-code-hide" href="#" v-if="inviteCodeGenerated" v-on:click="inviteCodeGenerated = null">Hide</a>
    </div>
    <div>
      <template v-for="user in users">
        <div class="col-sm-4 col-md-3">
          <div class="box box-primary">
            <div class="box-header with-border text-center">
              <a v-on:click="getCustomerData(user.id)" class="customer-name-title" href="#">
                <h3 class="box-title">@{{user.first_name}} @{{user.last_name}}</h3>
              </a>
              <div class="box-body">
                <a v-on:click="getCustomerData(user.id)"  href="#">
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
                <h3 class="modal-title" id="CustomerinfoModal">@{{user.first_name}} @{{user.last_name | setPossessive}} Info</h3>
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
                        <button v-on:click="RedeemDeal(deal.id)" data-dismiss="modal" class="btn btn-block btn-success pull-right">Redeem!</button>
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
<form><input type="hidden" name="_token" value="{{ csrf_token() }}"></form>
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
          deals: [],
          lastPurchase: {},
          lastViewedPost: null,
          recentBookmarked: null,
          recentShared: null,
          lastItemsPurchased: [],
          inviteCodeGenerated: null
        },

        mounted: function() {

          $('#CustomerinfoModal').on('shown.bs.modal', function (event) {
            console.log('something');
            customer.drawChart();
          });

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
            console.log(data);
            if ( data.user) {
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
            this.getRedeemableDeals(activeCustomer.id);
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
          getCustomerData: function(customerId) {
            var businessId = '{{ $profile->id }}'

            $.ajax({
              method: 'POST',
              url: '/user/purchases',
              data: {
                'customerId' : customerId,
                'businessId' : businessId
              },
              success: function(data) {
                var dataStorage = customer.$data;
                dataStorage.purchases = data.purchases;
                dataStorage.lastViewedPost = data.lastViewedPost;
                dataStorage.recentBookmarked = data.recentBookmarkedPost;
                dataStorage.recentShared = data.recentSharedPost;
                if (dataStorage.purchases.length !== 0 ) {
                  dataStorage.lastPurchase = dataStorage.purchases[0];
                  dataStorage.lastItemsPurchased = JSON.parse(dataStorage.purchases[0].products);
                  $('#CustomerinfoModal').modal('show');
                } else {
                  dataStorage.lastPurchase = null;
                  dataStorage.lastItemsPurchased = null;
                }
              },
              error: function(data) {
                console.log(data);
              }
            })
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
                var dataStorage = customer.$data;
                var deals = dataStorage.deals;
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
              }
            })
          }
        }
      })
    </script>
@stop






