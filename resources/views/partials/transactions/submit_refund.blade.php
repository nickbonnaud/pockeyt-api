<form action="{{ route('refund.submit') }}" method="post">
  <input type="hidden" name="products_old" :value="JSON.stringify(selectedReceiptItems)">
  <input type="hidden" name="tax_old" :value="totalTax">
  <input type="hidden" name="net_sales_old" :value="subTotal">
  <input type="hidden" name="total_old" :value="totalBill">
  <input type="hidden" name="products_new" :value="JSON.stringify(refundReceiptItems)">
  <input type="hidden" name="tax_new" :value="totalTaxRefund">
  <input type="hidden" name="net_sales_new" :value="subTotalRefund">
  <input type="hidden" name="total_new" :value="totalBillRefund">
  <input type="hidden" name="splash_id" :value="selectedReceiptId">
  {{ csrf_field() }}
  <input type="submit" value="Process Refund" class="btn btn-success btn-block btn-lg media">
</form>
