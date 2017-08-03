@extends('layoutDashboard')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      User Profile
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('profiles.show', ['profiles' => Crypt::encrypt($user->profile->id)]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">User Profile</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Default box -->
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <div class="change-pass">
            <a href="#" class="text-center" data-toggle="modal" data-target="#userPasswordModal">
              <b>Change</b> Password
            </a>
          </div>
          @if(is_null($user->photo_path))
            <img src="{{ asset('/images/icon-profile-photo.png') }}" class="profile-user-img img-responsive img-circle" alt="User Image">
            <div class="title-space text-center">
            <a href="#" class="text-center" data-toggle="modal" data-target="#userPhotoModal">
          		<b>Add</b> Profile Photo
          	</a>
          	</div>
          @else
            <img src="{{ $user->photo_path }}" class="profile-user-img img-responsive img-circle" alt="User Image">
            <div class="title-space text-center">
              <a href="#" class="text-center" data-toggle="modal" data-target="#userPhotoModal">
                <b>Change</b> Profile Photo
              </a>
            </div>
          @endif
          <ul class="list-group list-group-unbordered">
          	<li class="list-group-item">
          		<b>First Name</b>
          		<p class="pull-right">{{ $user->first_name }}</p>
          	</li>
          	<li class="list-group-item">
          		<b>Last Name</b>
          		<p class="pull-right">{{ $user->last_name }}</p>
          	</li>
          	<li class="list-group-item">
          		<b>Email</b>
          		<p class="pull-right">{{ $user->email }}</p>
          	</li>
          </ul>
          <a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#userInfoModal">
          	<b>Edit</b>
          </a>
        </div>
      </div>
      @include ('errors.form')
    </div>
    <div class="col-md-6" id="main">
      <input class="form-control" 
      v-validate="{
        rules: 
            { 
              regex: /^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%=@&?]).*$/,
              required: true,
              confirmed: 'password_confirm',
              min: 9,
              max: 72, 
            }
        }"
      name="new_password" type="password" :class="{'input': true, 'is-danger': errors.has('new_password') }" required>

      <input class="form-control" :class="{'input': true, 'is-danger': errors.has('new_password') }" name="password_confirm" type="password" required>

      <span v-show="errors.has('new_password')" class="help is-danger">@{{ errors.first('new_password') }}</span>
    </div>
    <!-- /.box -->
  </section>
  <!-- /.content -->
</div>
<div class="modal fade" id="userInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header-timeline">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="userPasswordModalLabel">Edit Info</h4>
      </div>
      <div class="modal-body-customer-info">
        {!! Form::open(['method' => 'PATCH', 'route' => ['users.update', $user->id], 'class' => 'form-horizontal']) !!}
          @include ('users.form')
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="userPasswordModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header-timeline">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="userPasswordModalLabel">Change Password</h4>
      </div>
      <div class="modal-body-customer-info">
        {!! Form::open(['method' => 'PATCH', 'route' => ['users.credentials', $user->id], 'class' => 'form-horizontal']) !!}
          @include ('users.passwordForm')
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="userPhotoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header-timeline">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="userPhotoModalLabel">Change User Photo</h4>
      </div>
      <div class="modal-body-customer-info">
          <div class="box-body">
            <p><label>Click or Drag-n-Drop your Profile Photo Here</label></p>
             <form id="uploadProfilePhoto" action="{{ route('users.photos', ['users' => $user->id]) }}" method="POST" class="dropzone">
                  {{ csrf_field() }}
              </form>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.content-wrapper -->
@stop

@section('scripts.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.js"></script>

<script>
    const dict = {
          en: {
            custom: {
              password: {
                  regex: 'Password does not meet requirements'
              }
            }
          }
        };
    var main = new Vue({
      el: '#main',

      mounted: function() {
        VeeValidate.Validator.updateDictionary(dict);
        Vue.use(VeeValidate);
      }
    });

  Dropzone.options.uploadProfilePhoto = {
      paramName: 'photo',
      maxFilesize: 3,
      acceptedFiles: '.jpg, .jpeg, .png, .bmp',
      init: function() {
          this.on('success', function() {
              window.location.reload();
          });
      }
  };
</script>
@stop