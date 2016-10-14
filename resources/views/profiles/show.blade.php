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
                Blank page
                <small>it all starts here</small>
              </h1>
              <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="#">Examples</a></li>
                <li class="active">Blank page</li>
              </ol>
            </section>

            <!-- Main content -->
            <section class="content">

              <!-- Default box -->
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Title</h3>

                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                      <i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                      <i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body">
                  Start creating your amazing application!
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                  Footer
                </div>
                <!-- /.box-footer-->
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

    <script>
      (function () {
        var pusher = new Pusher('f4976d40a137b96b52ea', {
          encrypted: true
        });
        var bizChannel = {!! 'business'.$profile->id !!}
        var channel = pusher.subscribe(bizChannel);

        channel.bind('App\\Events\\CustomerEnterRadius', function(data) {
          console.log(data);
        });
      })();
    </script>
@stop






