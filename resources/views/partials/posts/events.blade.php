@if(count($posts) > 0)
    @foreach($posts as $post)
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><a href="{{ route('posts.show', ['posts' => $post->id]) }}">{{ str_limit($post->title, 85) }}</a></h3>
                <p class="event-date pull-right">Date of Event: {{ $post->event_date }}</p>
            </div>
            <div class="box-body">
                    @if(! is_null($post->photo_path))
                        <img src="{{ $post->photo_path}}">
                        <hr>
                    @endif
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
    @endforeach
@endif