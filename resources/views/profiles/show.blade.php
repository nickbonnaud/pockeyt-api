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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.js"></script>

    <script>
        Dropzone.options.uploadLogo = {
            paramName: 'photo',
            maxFilesize: 3,
            acceptedFiles: '.jpg, .jpeg, .png, .bmp',
            init: function() {
                this.on('success', function() {
                    window.location.reload();
                });
            }
        };
        Dropzone.options.uploadHero = {
            paramName: 'photo',
            maxFilesize: 3,
            acceptedFiles: '.jpg, .jpeg, .png, .bmp',
            init: function() {
                this.on('success', function() {
                    window.location.reload();
                });
            }
        };

        $(function() {
            $( "#event_date_pretty" ).datepicker({
                dateFormat: "DD, d MM, yy",
                altField: "#event_date",
                altFormat: "yy-mm-dd"
            });
        });
    </script>
@stop