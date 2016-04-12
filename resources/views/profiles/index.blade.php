@extends('layout')


@section('content')

    <div class="row">
        <div class="col-md-12">

            <h1>All Profiles</h1>
            
            <hr>

            <div class="alert alert-info">
                All created profiles are listed below along with their approval status and related information.
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            @if(count($profiles) > 0)
                @foreach($profiles as $profile)

                    <div class="profile-list-item" id="profile-{{ $profile->id }}">
                        <div class="row profile-list-row">
                            <div class="col-md-3 text-center">

                                @if(is_null($profile->logo))
                                    <strong class="profile-list-item-logo">No logo</strong>
                                @else
                                    <img class="profile-list-item-logo" src="{{ $profile->logo->url }}">
                                @endif

                                @include('partials.profiles.admin_approval_status')
                                @include('partials.profiles.admin_featured_status')

                                <table class="table profile-list-item-table">
                                    <tbody>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $profile->created_at }} UTC</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At</th>
                                        <td>{{ $profile->updated_at }} UTC</td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                            <div class="col-md-9">

                                <div class="profile-list-item-header" {!! !is_null($profile->hero) ? 'style="background-image: url(\'' . $profile->hero->url . '\');"' : '' !!}>
                                    <div class="profile-list-item-header-screen"></div>
                                    <h2>{{ $profile->business_name }}</h2>
                                    <a href="{{ route('profiles.show', ['profiles' => $profile->id]) }}" class="btn btn-primary profile-list-item-view-button">View profile</a>
                                </div>

                                <div class="profile-list-item-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Website:</strong> <a target="_blank" href="{{ $profile->website }}">{{ $profile->website }}</a></p>
                                            <p><strong>Description:</strong></p>
                                            <textarea class="form-control" readonly="readonly">{{ $profile->description }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Owner's name:</strong> {{ $profile->owner->name }}</p>
                                            <p><strong>Owner's email:</strong> <a href="mailto:{{ $profile->owner->email }}">{{ $profile->owner->email }}</a></p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                @endforeach
            @else

                <div class="alert alert-warning text-center">No profiles found.</div>

            @endif

        </div>
    </div>

@stop