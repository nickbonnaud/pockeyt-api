@if(count($blogs) > 0)
    @foreach($blogs as $blog)
        <article>
            <div style="display:inline-block;">
                <h3><a href="{{ route('blogs.show', ['blogs' => $blog->id]) }}">{{ $blog->blog_title }}</a></h3>
                <div>
                    {{ $blog->published_at->diffForHumans() }}
                    by <strong>{{ $blog->author }}</strong>

                    @if($signedIn && ($isAdmin))
                        <a href="{{ route('blogs.edit', ['blogs' => $blog->id])  }}" class="btn btn-info btn-xs">Edit Blog</a>
                        @include('partials.blogs.delete')
                    @endif
                </div>
            </div>
        </article>
    @endforeach

@else

    <div class="text-center alert alert-warning">No blogs to show.</div>

@endif