@extends('layout')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <h1>Update Business Profile</h1>

            <hr>

            @if($signedIn && $isAdmin && !$user->owns($profile))
                <div class="alert alert-warning">
                    Heads up! You're logged in as an administrator and editing another user's profile.
                </div>
            @endif

            <div class="col-md-6">
                {!! Form::model($profile, ['method' => 'PATCH', 'route' => ['profiles.update', $profile->id]]) !!}
                    @include ('profiles.form')
                {!! Form::close() !!}
            </div>
        </div>

    </div>
@stop