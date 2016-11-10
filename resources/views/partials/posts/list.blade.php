@if(count($posts) > 0)
    @foreach($posts as $post)
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <!-- <h3 class="box-title"><a href="{{ route('posts.show', ['posts' => $post->id]) }}">{{ $post->message }}</a></h3> -->
                    <h3 class="box-title">{{ $post->message }}</h3>
                </div>
                <div class="box-body">
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
        </div>
    @endforeach
@endif