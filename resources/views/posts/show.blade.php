@extends('layoutPost')

@section('content')

    <div class="row">
        <div class="col-md-12">

            @if(is_null($post))

                <h2 class="text-center">Sorry! Looks like this post was deleted! :(</h2>

            @else

                <img class="photoLogo" src="{{ $profile->logo->url }}">
                <p class="partnername">{{ $profile->business_name }}</p>
                <p class="postTitle">{{ $post->title }}</p>

                <p><img class="postPhoto" src="{{ $post->photo_path }}"></p>
                <hr>
                <article class="postText">
                    {!!  $post->formatted_body !!}
                </article>
                <hr>
                <div class="footer-date">{{ $post->published_at->diffForHumans() }}</div>
                <p class="signature">- Brought to you by <a href="http://www.pockeyt.com/">Pockeyt</a>

            @endif

        </div>
    </div>

@stop