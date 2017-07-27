<form action="{{ route('refund.submit_all') }}" method="post">
  <input type="hidden" name="id" :value="selectedReceiptId">
  {{ csrf_field() }}
  <input type="submit" value="Refund All" class="btn btn-block btn-success btn-xs">
</form>
