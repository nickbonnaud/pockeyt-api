@if(isset($transaction))
  <form action="{{ route('bill.update', ['transactionId' => $transaction->id]) }}" method="patch">
   	<input type="hidden" name="user_id" value="{{ $customer->id }}">
    <input type="hidden" name="paid" value="false">
    <input type="hidden" name="products" value="@{{ JSON.stringify(this.bill) }}">
    <input type="hidden" name="total" value="@{{ totalBill }}">
    {{ csrf_field() }}
    <input type="submit" value="Keep Open" class="btn btn-block btn-primary btn-xs">
  </form>
@else
	<form action="{{ route('bill.store) }}" method="post">
    <input type="hidden" name="user_id" value="{{ $customer->id }}">
    <input type="hidden" name="paid" value="false">
    <input type="hidden" name="products" value="@{{ JSON.stringify(this.bill) }}">
    <input type="hidden" name="total" value="@{{ totalBill }}">
    {{ csrf_field() }}
    <input type="submit" value="Keep Open" class="btn btn-block btn-primary btn-xs">
  </form>
@endif