@if(count($posts) > 0)
    @foreach($posts as $post)
        <div class="box box-default">
            <div class="box-header with-border">
                <a href="{{ route('posts.show', ['posts' => $post->id]) }}">
                    <h3 class="box-title">{{ str_limit($post->message, 85) }}</h3>
                </a>
            </div>
            <div class="box-body">
                @if(! is_null($post->photo_path))
                    <div class="text-center">
                        <img src="{{ $post->photo_path}}">
                    </div>
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