<div class="form-group">
  <label for="method" class="col-sm-2 control-label">Account Type</label>
  <div class="col-sm-10">
    {!! Form::select('method', [
      '8' => 'Sole Proprietor',
      '9' => 'LLC',
      '10' => 'Partnership',
      '11' => 'Corporation'
      ], $account->method, ['class' => 'form-control']) 
    !!}
  </div>
</div>

<div class="form-group">
  <label for="accountNumber" class="col-sm-2 control-label">Full Account Number</label>
  <div class="col-sm-10">
    <input type="text" name="accountNumber" class="form-control" id="accountNumber" required>
  </div>
</div>
<div class="form-group">
 <label for="routing" class="col-sm-2 control-label">Full Routing Number</label>
  <div class="col-sm-10">
    <input type="integer" name="routing" class="form-control" id="routing" required>
  </div>
</div>
<div class="modal-footer modal-footer-form-tags">
  <div class="form-group">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary btn-form-footer">Save changes</button>
  </div>
</div>