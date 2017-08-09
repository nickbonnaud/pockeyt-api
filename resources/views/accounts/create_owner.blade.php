@extends('layout')
@section('content')
  <div class="row" id="owner">
    <div class="col-md-12">
      <h1>Payment Account Setup</h1>
      <h4>Business Owner Info</h4>
      <hr>

      @if($isAdmin)
          <div class="alert alert-warning">
              Heads up! You're logged in as an administrator. This form will create a profile for you which is likely
              not what you want to do.
          </div>
      @endif

      {!! Form::open(['method' => 'PATCH', 'route' => 'accounts.setOwner']) !!}
          @include ('errors.form')
          @include ('accounts.form_create_owner')
      {!! Form::close() !!}
    </div>
  </div>
@stop
@section('scripts.footer')
  <script>
      
    Vue.use(VueMask.VueMaskPlugin);
    var owner = new Vue({
      el: '#owner',

      data: {
        ownership: '',
        indivState: '',
        indivZip: '',
        ssn: ''
      },
    });

  </script>
@stop