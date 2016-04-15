@extends('layout')


@section('content')
    <div class="jumbotron">
	    <div class="row">
		    <div class="col-sm-8">
		        <h1>Pockeyt Business</h1>
		
		        <p>An easier way to find and connect with your customers</p>
		    </div>
		    <div class="col-sm-4 loginbuttons">
		        @if ($signedIn)
		            @if($hasProfile)
		                <a href="{{ route('profiles.show', ['profiles' => $user->profile->id]) }}" class="btn btn-primary">View Profile</a>
		            @else
		                @if($isAdmin)
		                    <a href="{{ route('profiles.index') }}" class="btn btn-primary">View All Profiles</a>
		                @else
		                    <a href="{{ route('profiles.create') }}" class="btn btn-primary">Create Profile</a>
		                @endif
		            @endif
		        @else
		            <a href="{{ route('auth.login') }}" class="btn btn-primary">Login</a>
		            <a href="{{ route('auth.register') }}" class="btn btn-primary">Sign Up</a>
		        @endif
		    </div>
	    </div>
    </div>
@stop