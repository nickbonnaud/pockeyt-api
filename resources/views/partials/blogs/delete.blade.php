@if($signedIn && ($isAdmin ))
    <form action="{{ route('blogs.destroy', ['blogs' => $blog->id]) }}" method="post" class="form-inline" style="display: inline-block;">
        <input type="hidden" name="_method" value="DELETE">
        {{ csrf_field() }}
        <input type="submit" value="Delete" class="btn btn-danger btn-xs">
    </form>
@endif