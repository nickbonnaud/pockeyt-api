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

            <form method="POST" action="{{ route('profiles.store') }}" enctype="multipart/form-data" class="col-md-6">
                @include ('errors.form')
                @include ('profiles.form')
            </form>

        </div>

    </div>
@stop