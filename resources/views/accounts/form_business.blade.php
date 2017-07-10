<div class="form-group">
  <label for="legalBizName" class="col-sm-2 control-label">Legal Business Name</label>
  <div class="col-sm-10">
    <input type="text" name="legalBizName" class="form-control" id="legalBizName" value="{{ $account->legalBizName }}" required>
  </div>
</div>
<div class="form-group">
  <label for="businessType" class="col-sm-2 control-label">Business Type</label>
  <div class="col-sm-10">
    {!! Form::select('businessType', [
      '0' => 'Sole Proprietor',
      '2' => 'LLC',
      '3' => 'Partnership',
      '1' => 'Corporation',
      '4' => 'Association'
      ], $account->businessType, ['class' => 'form-control']) 
    !!}
  </div>
</div>
<div class="form-group">
 <label for="bizTaxId" class="col-sm-2 control-label">Business Tax ID (EIN)</label>
  <div class="col-sm-10">
    <input data-inputmask="'mask': '99-9999999'" type="tel" name="bizTaxId" class="form-control" id="bizTaxId" value="{{ $account->bizTaxId }}" required>
  </div>
</div>
<div class="form-group">
 <label for="established" class="col-sm-2 control-label">Business Established</label>
  <div class="col-sm-10">
    <input type="date" name="established" class="form-control" id="established" value="{{ $account->established }}" required>
  </div>
</div>
<div class="form-group">
 <label for="annualCCSales" class="col-sm-2 control-label">Annual Credit Card Sales</label>
  <div class="col-sm-10">
    <div class="input-group">
      <span class="input-group-addon">$</span>
      <input data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '$ ', 'placeholder': '0'" type="tel" name="annualCCSales" class="form-control" id="annualCCSales" value="{{ $account->annualCCSales }}" required>
    </div>
  </div>
</div>
<div class="form-group">
 <label for="bizStreetAdress" class="col-sm-2 control-label">Business Street Address</label>
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
    <input data-inputmask="'mask': '99999'" type="tel" name="bizZip" class="form-control" id="bizZip" value="{{ $account->bizZip }}" required>
  </div>
</div>
<div class="form-group">
 <label for="phone" class="col-sm-2 control-label">Business Phone Number</label>
  <div class="col-sm-10">
    <input data-inputmask="'mask': '(999) 999-9999'" type="tel" name="phone" class="form-control" id="phone" value="{{ $account->phone }}" required>
  </div>
</div>
<div class="form-group">
 <label for="accountEmail" class="col-sm-2 control-label">Business Email</label>
  <div class="col-sm-10">
    <input type="email" name="accountEmail" class="form-control" id="accountEmail" value="{{ $account->accountEmail }}" required>
  </div>
</div>
<div class="modal-footer modal-footer-form-tags">
  <div class="form-group">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary btn-form-footer">Save changes</button>
  </div>
</div>