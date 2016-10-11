<div class="box-body">
  <div class="form-group">
    <label for="legalBizName" class="col-sm-2 control-label">Legal Business Name</label>
    <div class="col-sm-10">
      <input type="text" name="legalBizName" class="form-control" id="legalBizName" value="{{ $account->legalBizName }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="bizTaxId" class="col-sm-2 control-label">Business Tax ID</label>
    <div class="col-sm-10">
      <input type="integer" name="bizTaxId" class="form-control" id="bizTaxId" value="{{ $account->bizTaxId }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="bizStreetAdress" class="col-sm-2 control-label">Street Address</label>
    <div class="col-sm-10">
      <input type="string" name="bizStreetAdress" class="form-control" id="bizStreetAdress" value="{{ $account->bizStreetAdress }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="bizCity" class="col-sm-2 control-label">City</label>
    <div class="col-sm-10">
      <input type="string" name="bizCity" class="form-control" id="bizCity" value="{{ $account->bizCity }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="bizState" class="col-sm-2 control-label">State</label>
    <div class="col-sm-10">
      <input type="string" name="bizState" class="form-control" id="bizState" value="{{ $account->bizState }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="bizZip" class="col-sm-2 control-label">Zip</label>
    <div class="col-sm-10">
      <input type="string" name="bizZip" class="form-control" id="bizZip" value="{{ $account->bizZip }}" required>
    </div>
  </div>
  <div class="modal-footer">
    <div class="form-group">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
  </div>
</div>