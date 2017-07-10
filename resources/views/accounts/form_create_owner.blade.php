<div class="form-group">
    <label for="accountUserFirst">Business Owner First:</label>
    <input type="text" name="accountUserFirst" id="accountUserFirst" value="{{ $user->first_name }}" placeholder="First Name of Account Holder" class="form-control" required>
</div>

<div class="form-group">
    <label for="accountUserLast">Last:</label>
    <input type="text" name="accountUserLast" id="accountUserLast" value="{{ $user->last_name }}" placeholder="Last Name of Account Holder" class="form-control" required>
</div>

<div class="form-group">
   <label for="dateOfBirth">Date of Birth</label>
      <input type="date" name="dateOfBirth" class="form-control" id="dateOfBirth" required>
</div>

<div class="form-group">
    <label for="ownership">Percentage of Business Owned</label>
    <input data-inputmask="'mask': '[9]99%'" type="tel" name="ownership" id="ownership" placeholder="100" class="form-control" required>
</div>

<div class="form-group">
    <label for="indivStreetAdress">Owner Home Address</label>
    <input type="text" name="indivStreetAdress" id="indivStreetAdress" class="form-control" required>
</div>

<div class="form-group">
    <label for="indivCity">City</label>
    <input type="text" name="indivCity" id="indivCity" class="form-control" required>
</div>

<div class="form-group">
    <label for="indivState">State</label>
    <input type="text" name="indivState" id="indivState" placeholder="NC" maxlength="2" class="form-control" required>
</div>

<div class="form-group">
    <label for="indivZip">Zip</label>
    <input type="integer" name="indivZip" id="indivZip" class="form-control" required>
</div>

<div class="form-group">
    <label for="ownerEmail">Owner Email:</label>
    <input type="email" name="ownerEmail" id="ownerEmail" value="{{ $user->email }}" placeholder="Email of Owner" class="form-control" required>
</div>

<div class="form-group">
    <label for="ssn">Owner SSN:</label>
    <input type="password" min="9" max="9" name="ssn" id="ssn" class="form-control" required>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary pull-right">Next</button>
</div>