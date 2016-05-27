@extends('layout')

@section('content')

    @if(is_null($profile))

        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center">Profile not found.</h2>
            </div>
        </div>

    @else

        <div class="row">
            <div class="col-md-4">
                <h1>{{ $profile->business_name }}</h1>

                @if ($signedIn && $user->owns($profile) && !$isAdmin)
                    <hr>
                    @if($profile->approved)
                        <div class="alert alert-success">
                            Your profile is <strong>approved</strong>. Users will be able to see it and any of your posts.
                        </div>
                    @else
                        <div class="alert alert-danger">
                            Your profile is <strong>not approved</strong>. Users will not be able to see it or any of your
                            posts until an administrator reviews it.
                        </div>
                    @endif

                    @if($profile->featured)
                        <div class="alert alert-info">
                            Your profile is featured!
                        </div>
                    @endif
                @endif

                @if($signedIn && $isAdmin)
                    <hr>
                    @include('partials.profiles.admin_approval_status')
                    @include('partials.profiles.admin_featured_status')
                @endif

                <hr>

                <div class="description">{!! nl2br($profile->description) !!}</div>

                <hr>

                <h4>Tags:</h4>
                <ul>
                    @foreach ($profile->tags as $tag)
                        <li>{{ $tag->name }}</li>
                    @endforeach
                </ul>

                <hr>

                <p>Review URL: {{ $profile->review_url }}</p>
                <p>Review Teaser: {{ $profile->review_intro }}</p>

                <hr>

                <a href="{{ $profile->website }}" class="btn btn-block btn-primary">Visit website</a>

                @if($signedIn && ($user->owns($profile) || $user->is_admin))
                    <a href="{{ route('profiles.edit', ['profiles' => $profile->id])  }}" class="btn btn-block btn-info">Edit Profile</a>
                @endif

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <p><label>Logo</label></p>
                        @if(is_null($profile->logo))
                            <form id="uploadLogo" action="{{ route('profiles.photos', ['profiles' => $profile->id]) }}" method="POST" class="dropzone">
                                {{ csrf_field() }}
                                <input type="hidden" name="type" value="logo">
                            </form>
                        @else
                            <p><img src="{{ $profile->logo->url }}"></p>
                            @if($user && ($user->owns($profile) || $isAdmin))
                                <form action="{{ route('profiles.photos', ['profiles' => $profile->id]) }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="type" value="logo">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger btn-block">Delete</button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <p><label>Hero/Banner</label></p>
                        @if(is_null($profile->hero))
                            <form id="uploadHero" action="{{ route('profiles.photos', ['profiles' => $profile->id]) }}" method="POST" class="dropzone">
                                {{ csrf_field() }}
                                <input type="hidden" name="type" value="hero">
                            </form>
                        @else
                            <p><img src="{{ $profile->hero->url }}"></p>
                            @if($user && ($user->owns($profile) || $isAdmin))
                                <form action="{{ route('profiles.photos', ['profiles' => $profile->id]) }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="type" value="hero">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger btn-block">Delete</button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                @if($user && $user->owns($profile))
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Create a Post</h2>
                            <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
                                @include ('posts.form')
                                @include ('errors.form')
                            </form>
                        </div>
                    </div>
                    <hr>
                @endif
                <div class="row">
                    <div class="col-lg-12">
                        <h2>Posts</h2>
                        @include('partials.posts.list', ['posts' => $profile->posts, 'no_icons' => true])
                    </div>
                </div>
            </div>
        </div>

    @endif
@stop

@section('scripts.footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.js"></script>

    <script>
        Dropzone.options.uploadLogo = {
            paramName: 'photo',
            maxFilesize: 3,
            acceptedFiles: '.jpg, .jpeg, .png, .bmp',
            init: function() {
                this.on('success', function() {
                    window.location.reload();
                });
            }
        };
        Dropzone.options.uploadHero = {
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