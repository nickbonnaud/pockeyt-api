<form action="{{ route('bill.charge') }}" method="post">
  <input type="hidden" name="user_id" value="{{ $customer->id }}">
  <input type="hidden" name="paid" value="false">
  <input type="hidden" name="products" :value="JSON.stringify(bill)">
  <input type="hidden" name="tax" :value="totalTax">
  <input type="hidden" name="net_sales" :value="subTotal">
  <input type="hidden" name="total" :value="totalBill">
  {{ csrf_field() }}
  <input type="submit" value="Charge Customer" class="btn btn-block btn-success">
</form>
