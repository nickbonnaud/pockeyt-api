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
 <label for="dateOfBirth" class="col-sm-2 control-label">Date of Birth</label>
  <div class="col-sm-10">
    <input type="date" name="dateOfBirth" class="form-control" id="dateOfBirth" value="{{ $account->dateOfBirth }}" required>
  </div>
</div>
<div class="form-group">
 <label for="ownership" class="col-sm-2 control-label">Percentage Ownership</label>
  <div class="col-sm-10">
    <input data-inputmask="'mask': '9[9][9]%', 'greedy': 'false'" type="tel" name="ownership" class="form-control" id="ownership" value="{{ $account->ownership / 100 }}" required>
  </div>
</div>
<div class="form-group">
 <label for="indivStreetAdress" class="col-sm-2 control-label">Owner Home Address</label>
  <div class="col-sm-10">
    <input type="text" name="indivStreetAdress" class="form-control" id="indivStreetAdress" value="{{ $account->indivStreetAdress }}" required>
  </div>
</div>
<div class="form-group">
 <label for="indivCity" class="col-sm-2 control-label">City</label>
  <div class="col-sm-10">
    <input type="text" name="indivCity" class="form-control" id="indivCity" value="{{ $account->indivCity }}" required>
  </div>
</div>
<div class="form-group">
 <label for="indivState" class="col-sm-2 control-label">State</label>
  <div class="col-sm-10">
    <input data-inputmask="'mask': 'aa'" type="text" name="indivState" class="form-control" id="indivState" value="{{ $account->indivState }}" required>
  </div>
</div>
<div class="form-group">
 <label for="indivZip" class="col-sm-2 control-label">Zip</label>
  <div class="col-sm-10">
    <input data-inputmask="'mask': '99999'" type="tel" name="indivZip" class="form-control" id="indivZip" value="{{ $account->indivZip }}" required>
  </div>
</div>
<div class="form-group">
 <label for="ownerEmail" class="col-sm-2 control-label">Owner Email</label>
  <div class="col-sm-10">
    <input type="email" name="ownerEmail" class="form-control" id="ownerEmail" value="{{ $account->ownerEmail }}" required>
  </div>
</div>
<div class="form-group">
 <label for="ssn" class="col-sm-2 control-label">Full SSN</label>
  <div class="col-sm-10">
    <input data-inputmask="'mask': '999-99-9999'" type="tel" name="ssn" class="form-control" id="ssn" value="XXXXX{{$account->ssn}}" required>
  </div>
</div>
<div class="modal-footer modal-footer-form-tags">
  <div class="form-group">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary btn-form-footer">Save changes</button>
  </div>
</div>