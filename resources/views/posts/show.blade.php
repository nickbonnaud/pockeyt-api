@extends('layoutPost')
<head>
    <meta property="og:type" content="article" />
    <meta property="og:url" content="https:www.pockeyt-test.com/posts/{{ $post->id }}" /> 
    <meta property="og:title" content="{{ $post->message }}" />
    @if(! is_null($post->photo_path))
        <meta property="og:image"  content="{{ $post->photo_path }}" />
    @endif
</head>

@section('content')

    <div class="row">
        <div class="col-md-12">
            <img class="photoLogo" src="{{ $profile->logo->url }}">
            <span class="partnername">{{ $profile->business_name }}</span>
            @if(! is_null($post->title))
                <p class="postTitle">{{ $post->title }}</p>
            @endif

            <img class="postPhoto" src="{{ $post->photo_path }}">
            <hr>
            @if(! is_null($post->body))
                <article class="postText">
                    {!!  $post->formatted_body !!}
                </article>
            @else
                <article class="postText">
                    {{ $post->message }}
                </article>
            @endif
            <hr>
            <div class="footer-date">{{ $post->published_at->diffForHumans() }}</div>
            <p class="signature">- Brought to you by <a href="http://www.pockeyt.com/">Pockeyt</a>

        </div>
    </div>

@stop