<div class="form-group">
    <label for="method">Account Type</label>
    <select name="method" id="method" class="form-control" required>
        <option value="8">Checking Account</option>
        <option value="9">Savings Account </option>
        <option value="10">Corporate Checking Account</option>
        <option value="11">Corporate Savings Account</option>
    </select>
</div>

<div class="form-group">
    <label for="accountNumber">Account Number</label>
    <input type="integer" name="accountNumber" id="accountNumber" class="form-control" required>
</div>

<div class="form-group">
    <label for="routing">Routing Number</label>
    <input type="integer" name="routing" id="routing" class="form-control" required>
</div>

<div class="form-group">
  <div class="checkbox">
    <label for="ToS">
      <input type="checkbox" name="ToS" id="ToS" value="true" required>
      Agree to <a href="#" data-toggle="modal" data-target="#ToSModal">Terms of Service and Privacy Policy</a>
    </label>
  </div>
</div>

<hr>

<div class="form-group">
    <button type="submit" class="btn btn-primary pull-right">Finish</button>
</div>

<div class="modal fade" id="ToSModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="ToSModal">Account Terms of Service and Privacy Policy</h4>
      </div>
      <div class="modal-body">
        <p>1. Please review Pockeyt's Privacy Policy, found <a href="{{ route('app.privacyPolicy') }}" target="_blank">here</a></p>
        <p>2. Please review Pockeyt's End-User License Agreement, found <a href="{{ route('app.endPolicy') }}" target="_blank">here</a></p>
      </div>
    </div>
  </div>
</div>