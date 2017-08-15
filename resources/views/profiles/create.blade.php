@extends('layout')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <h1>Create Business Profile</h1>

            <hr>

            @if($isAdmin)
                <div class="alert alert-warning">
                    Heads up! You're logged in as an administrator. This form will create a profile for you which is likely
                    not what you want to do.
                </div>
            @endif

            {!! Form::open(['route' => 'profiles.store']) !!}
                @include ('errors.form')
                @include ('profiles.form_create')
            {!! Form::close() !!}
        </div>

    </div>
@stop