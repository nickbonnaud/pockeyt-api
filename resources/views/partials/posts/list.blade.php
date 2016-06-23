@if(count($posts) > 0)
    @foreach($posts as $post)
        <article>
            @if(!is_null($post->profile->logo) && (!isset($no_icons) || !$no_icons))
                <div style="display:inline-block; vertical-align:top;">
                    <img src="{{ $post->profile->logo->thumbnail_url }}">
                </div>
            @endif
            <div style="display:inline-block;">
                <h3><a href="{{ route('posts.show', ['posts' => $post->id]) }}">{{ $post->title }}</a></h3>
                @if($post->event_date !== '0000-00-00')

                    <p>Event date: {{ $post->event_date }}</p>
                @endif
                <div>
                    {{ $post->published_at->diffForHumans() }}
                    by
                    <a href="{{ route('profiles.show', ['profiles' => $post->profile->id]) }}">
                        <strong>{{ $post->profile->business_name }}</strong>
                    </a>
                    @if($signedIn && ($isAdmin || $user->profile->owns($post)))
                        @include('partials.posts.delete')
                    @endif
                </div>
            </div>
        </article>
    @endforeach

@else

    <div class="text-center alert alert-warning">No posts to show.</div>

@endif