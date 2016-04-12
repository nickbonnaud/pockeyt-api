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

            <form method="POST" action="{{ route('profiles.update', ['profiles' => $profile->id]) }}" enctype="multipart/form-data" class="col-md-6">
                <input type="hidden" name="_method" value="PUT">
                @include ('errors.form')
                @include ('profiles.form')
            </form>

        </div>

    </div>
@stop