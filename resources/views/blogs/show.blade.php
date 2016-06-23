@extends('layoutPost')

@section('content')

    <div class="row">
        <div class="col-md-12">

            @if(is_null($blog))

                <h2 class="text-center">Sorry! Looks like this post was deleted! :(</h2>

            @else
                <div class="blog-image-container">
                    <img class="photoHero" src="{{ $blog->blog_hero_url }}">
                    <img class="photoProfile" src="{{ $blog->blog_profile_url }}">
                </div>
                <p class="author">{{ $blog->author }}</p>
                <p class="author-description">{{ $blog->description }}</p>
                <p class="blog-title">{{ $blog->blog_title }}</p>

                <article class="blog-body">
                    {!! nl2br($blog->blog_body) !!}
                </article>
                <hr>
                <div class="footer-date">{{ $blog->published_at->diffForHumans() }}</div>
                <p class="signature">- Brought to you by <a href="http://www.pockeyt.com/">Pockeyt</a>
            @endif

        </div>
    </div>

@stop