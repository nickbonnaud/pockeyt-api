@if($signedIn && $isAdmin)
  <form action="{{ route('accounts.approve') }}" method="post">
    {{ csrf_field() }}
    <div class="form-group">
	    <label for="mcc">Merchant Category Code</label>
	    <input data-inputmask="'mask': '9999'" type="tel" name="mcc" id="mcc" class="form-control" required>
	    <input type="hidden" name="accountId" id="accountId" value="{{ $account->id }}">
		</div>
		<div class="form-group">
    	<input type="submit" value="Approve" class="btn btn-block btn-success btn-sm">
    </div>
  </form>
@endif