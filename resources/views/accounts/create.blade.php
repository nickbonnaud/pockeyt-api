@extends('layout')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <h1>Add Payment Account Details</h1>

            <hr>

            @if($isAdmin)
                <div class="alert alert-warning">
                    Heads up! You're logged in as an administrator. This form will create a profile for you which is likely
                    not what you want to do.
                </div>
            @endif

            {!! Form::open(['route' => 'accounts.store']) !!}
                @include ('errors.form')
                @include ('accounts.form_create')
            {!! Form::close() !!}
        </div>

    </div>
@stop