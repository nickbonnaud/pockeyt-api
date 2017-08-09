@extends('layout')

@section('content')

    <div class="row" id="payment">

        <div class="col-md-12">

            <h1>Payment Account Setup</h1>
            <h4>Business Info</h4>
            <hr>

            @if($isAdmin)
                <div class="alert alert-warning">
                    Heads up! You're logged in as an administrator. This form will create a profile for you which is likely
                    not what you want to do.
                </div>
            @endif

            {!! Form::open(['route' => 'accounts.setBusiness']) !!}
                @include ('errors.form')
                @include ('accounts.form_create')
            {!! Form::close() !!}
        </div>

    </div>
@stop
@section('scripts.footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
    <script src="{{ asset('/vendor/vMask/v-mask.min.js') }}"></script>
    <script>
        
        Vue.use(VueMask.VueMaskPlugin);
        var payment = new Vue({
            el: '#payment'
        });

    </script>
@stop