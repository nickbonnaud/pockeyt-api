<div class="form-group">
    <label for="legalBizName">Legal Business Name</label>
    <input type="text" name="legalBizName" id="legalBizName" placeholder="Example, Inc." class="form-control" required>
</div>
<div class="form-group">
    <label for="businessType">Business Type</label>
    <select name="businessType" id="businessType" class="form-control" required>
        <option value="0">Sole Proprietor</option>
        <option value="2">LLC</option>
        <option value="3">Partnership</option>
        <option value="1">Corporation</option>
        <option value="4">Association</option>
    </select>
</div>
<div class="form-group">
    <label for="bizTaxId">Federal Tax ID (EIN)</label>
    <input type="integer" name="bizTaxId" id="bizTaxId" placeholder="12-3456789" maxlength="10" class="form-control" required>
</div>
<div class="form-group">
    <label for="established">Date Business Established</label>
    <input type="date" name="established" class="form-control" id="established" required>
</div>
<div class="form-group">
    <label for="annualCCSales">Estimate of Annual Credit Card Sales</label>
    <input type="integer" name="annualCCSales" id="annualCCSales" class="form-control" required>
</div>
<div class="form-group">
    <label for="bizStreetAdress">Business Street Address</label>
    <input type="text" name="bizStreetAdress" id="bizStreetAdress" class="form-control" required>
</div>
<div class="form-group">
    <label for="bizCity">City</label>
    <input type="text" name="bizCity" id="bizCity" class="form-control" required>
</div>
<div class="form-group">
    <label for="bizState">State</label>
    <input type="text" name="bizState" id="bizState" placeholder="NC" maxlength="2" class="form-control" required>
</div>
<div class="form-group">
    <label for="bizZip">Zip</label>
    <input type="integer" name="bizZip" id="bizZip" class="form-control" required>
</div>
<div class="form-group">
    <label for="phone">Business Phone Number</label>
    <input type="integer" name="phone" id="phone" class="form-control" required>
</div>
<div class="form-group">
    <label for="accountEmail">Business Email</label>
    <input type="text" name="accountEmail" id="accountEmail" value="{{ $user->email }}" class="form-control" required>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary pull-right">Next</button>
</div>