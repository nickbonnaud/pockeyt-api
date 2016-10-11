<div class="box-body">
  <div class="form-group">
    <label for="accountNumber4" class="col-sm-2 control-label">Full Account Number</label>
    <div class="col-sm-10">
      <input type="text" name="accountNumber4" class="form-control" id="accountNumber4" value="{{ $account->accountNumber4 }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="routingNumber4" class="col-sm-2 control-label">Full Routing Number</label>
    <div class="col-sm-10">
      <input type="integer" name="routingNumber4" class="form-control" id="routingNumber4" value="{{ $account->routingNumber4 }}" required>
    </div>
  </div>
  <div class="modal-footer">
    <div class="form-group">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
  </div>
</div>