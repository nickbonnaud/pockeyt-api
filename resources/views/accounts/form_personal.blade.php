<div class="form-group">
  <label for="accountUserFirst" class="col-sm-2 control-label">First name</label>
  <div class="col-sm-10">
    <input type="text" name="accountUserFirst" class="form-control" id="accountUserFirst" value="{{ $account->accountUserFirst }}" required>
  </div>
</div>
<div class="form-group">
 <label for="accountUserLast" class="col-sm-2 control-label">Last name</label>
  <div class="col-sm-10">
    <input type="text" name="accountUserLast" class="form-control" id="accountUserLast" value="{{ $account->accountUserLast }}" required>
  </div>
</div>
<div class="form-group">
 <label for="accountEmail" class="col-sm-2 control-label">Email</label>
  <div class="col-sm-10">
    <input type="email" name="accountEmail" class="form-control" id="accountEmail" value="{{ $account->accountEmail }}" required>
  </div>
</div>
<div class="form-group">
 <label for="dateOfBirth" class="col-sm-2 control-label">Date of Birth</label>
  <div class="col-sm-10">
    <input type="date" name="dateOfBirth" class="form-control" id="dateOfBirth" value="{{ $account->dateOfBirth }}" required>
  </div>
</div>
<div class="form-group">
 <label for="last4" class="col-sm-2 control-label">Last 4 SSN</label>
  <div class="col-sm-10">
    <input type="integer" name="last4" class="form-control" id="last4" value="{{ $account->last4 }}" required>
  </div>
</div>
<div class="form-group">
 <label for="indivStreetAdress" class="col-sm-2 control-label">Street Address</label>
  <div class="col-sm-10">
    <input type="string" name="indivStreetAdress" class="form-control" id="indivStreetAdress" value="{{ $account->indivStreetAdress }}" required>
  </div>
</div>
<div class="form-group">
 <label for="indivCity" class="col-sm-2 control-label">City</label>
  <div class="col-sm-10">
    <input type="string" name="indivCity" class="form-control" id="indivCity" value="{{ $account->indivCity }}" required>
  </div>
</div>
<div class="form-group">
 <label for="indivState" class="col-sm-2 control-label">State</label>
  <div class="col-sm-10">
    <input type="string" name="indivState" class="form-control" id="indivState" value="{{ $account->indivState }}" required>
  </div>
</div>
<div class="form-group">
 <label for="indivZip" class="col-sm-2 control-label">Zip</label>
  <div class="col-sm-10">
    <input type="string" name="indivZip" class="form-control" id="indivZip" value="{{ $account->indivZip }}" required>
  </div>
</div>
<div class="modal-footer  modal-footer-form-tags">
  <div class="form-group">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary btn-form-footer">Save changes</button>
  </div>
</div>