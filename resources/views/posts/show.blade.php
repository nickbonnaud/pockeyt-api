@extends('layout')

@section('content')

    <div class="row">
        <div class="col-md-12">

            @if(is_null($post))

                <h2 class="text-center">Post not found.</h2>

            @else

                <h1>
                    {{ $post->title }}
                    <div class="pull-right">
                        @include('partials.posts.delete')
                    </div>
                </h1>

                <hr>

                <p><img src="{{ $post->photo_path }}"></p>

                <article>
                    {!!  $post->formatted_body !!}
                </article>

            @endif

        </div>
    </div>

@stop