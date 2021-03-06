@if($signedIn && ($isAdmin || $user->profile->owns($post)))
    <form action="{{ route('posts.destroy', ['posts' => $post->id]) }}" method="post" class="form-inline pull-right" style="display: inline-block;">
        <input type="hidden" name="_method" value="DELETE">
        {{ csrf_field() }}
        <input type="submit" value="Delete" class="btn btn-danger btn-flat btn-xs">
    </form>
@endif