<div class="form-group">
    <label for="accountUserFirst">Account Owner First:</label>
    <input type="text" name="accountUserFirst" id="accountUserFirst" value="{{ $user->first_name }}" placeholder="First Name of Account Holder" class="form-control" required>
</div>

<div class="form-group">
    <label for="accountUserLast">Account Owner Last:</label>
    <input type="text" name="accountUserLast" id="accountUserLast" value="{{ $user->last_name }}" placeholder="Last Name of Account Holder" class="form-control" required>
</div>

<div class="form-group">
    <label for="accountEmail">Account Owner Email:</label>
    <input type="email" name="accountEmail" id="accountEmail" value="{{ $user->email }}" placeholder="Email of Account Holder" class="form-control" required>
</div>
<div class="form-group">
   <label for="dateOfBirth">Date of Birth</label>
      <input type="date" name="dateOfBirth" class="form-control" id="dateOfBirth" required>
  </div>

<div class="form-group">
    <label for="last4">Account Owner last 4 SSN:</label>
    <input type="integer" name="last4" id="last4" placeholder="Last 4 of Account Holder's SSN" class="form-control" required>
</div>
<div class="form-group">
    <label for="indivStreetAdress">Account Owner Street Address</label>
    <input type="text" name="indivStreetAdress" id="indivStreetAdress" placeholder="Street Address of Account Holder" class="form-control" required>
</div>
<div class="form-group">
    <label for="indivCity">Account Owner City</label>
    <input type="text" name="indivCity" id="indivCity" class="form-control" required>
</div>
<div class="form-group">
    <label for="indivState">Account Owner State</label>
    <input type="text" name="indivState" id="indivState" placeholder="NC" class="form-control" required>
</div>
<div class="form-group">
    <label for="indivZip">Account Owner Zip</label>
    <input type="integer" name="indivZip" id="indivZip" class="form-control" required>
</div>
<div class="form-group">
    <label for="legalBizName">Legal Business Name</label>
    <input type="text" name="legalBizName" id="legalBizName" placeholder="Example, Inc." class="form-control" required>
</div>
<div class="form-group">
    <label for="bizTaxId">Tax ID associated with Business</label>
    <input type="integer" name="bizTaxId" id="bizTaxId" placeholder="123456789" class="form-control" required>
</div>
<div class="form-group">
    <label for="accountNumber4">Bank Account Number</label>
    <input type="integer" name="accountNumber4" id="accountNumber4" class="form-control" required>
</div>
<div class="form-group">
    <label for="routingNumber4">Routing Number</label>
    <input type="integer" name="routingNumber4" id="routingNumber4" class="form-control" required>
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
        <p>
            1. Pockeyt, Inc. uses Braintree, a division of PayPal, Inc. (Braintree) for payment processing services. By using the Braintree payment processing services you agree to the Braintree Payment Services Agreement available at <a href="https://www.braintreepayments.com/legal/gateway-agreement" target="_blank">https://www.braintreepayments.com/legal/gateway-agreement</a>, and the applicable bank agreement available at <a href="https://www.braintreepayments.com/legal/cea-wells" target="_blank">https://www.braintreepayments.com/legal/cea-wells</a>.
        </p>
        <p>2. Please review Pockeyt's Privacy Policy, found <a href="{{ route('app.privacyPolicy') }}" target="_blank">here</a></p>
        <p>2. Please review Pockeyt's End-User License Agreement, found <a href="{{ route('app.endPolicy') }}" target="_blank">here</a></p>
      </div>
    </div>
  </div>
</div>