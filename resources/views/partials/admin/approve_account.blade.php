@if($signedIn && $isAdmin)
  <form action="{{ route('accounts.approve', ['accounts' => $account->id]) }}" method="post">
    {{ csrf_field() }}
    <input type="submit" value="Approve" class="btn btn-block btn-success btn-sm">
  </form>
@endif