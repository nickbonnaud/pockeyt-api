<form action="{{ route('loyalty-programs.destroy', ['loyaltyProgram' => $loyaltyProgram->id]) }}" method="post">
    <input type="hidden" name="_method" value="DELETE">
    {{ csrf_field() }}
    <input type="submit" value="Delete" class="btn btn-danger">
</form>
