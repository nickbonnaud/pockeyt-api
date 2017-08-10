<div class="form-group">
    <label for="legalBizName">Legal Business Name</label>
    <input type="text" name="legalBizName" id="legalBizName" placeholder="Example, Inc." class="form-control" required>
</div>
<div class="form-group">
    <label for="businessType">Business Type</label>
    <select name="businessType" id="businessType" class="form-control" required>
        <option value="">Please select Business Type</option>
        <option value="0">Sole Proprietor</option>
        <option value="2">LLC</option>
        <option value="3">Partnership</option>
        <option value="1">Corporation</option>
        <option value="4">Association</option>
    </select>
</div>
<div class="form-group">
    <label for="bizTaxId">Federal Tax ID (EIN)</label>
    <input v-mask="'##-#######'" v-model="bizTaxId" type="tel" name="bizTaxId" id="bizTaxId" placeholder="12-3456789" class="form-control" required>
</div>
<div class="form-group">
    <label for="established">Date Business Established</label>
    <input type="date" name="established" class="form-control" id="established" required>
</div>
<div class="form-group">
    <label for="annualCCSales">Estimate of Annual Credit Card Sales</label>
    <money v-model="annualCCSales" v-bind="money" type="tel" name="annualCCSales" class="form-control" id="annualCCSales" required></money>
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
    <input v-mask="'AA'" v-model="bizState" type="text" name="bizState" id="bizState" placeholder="NC" maxlength="2" class="form-control" required>
</div>
<div class="form-group">
    <label for="bizZip">Zip</label>
    <input v-mask="'#####'" v-model="bizZip" type="tel" name="bizZip" id="bizZip" placeholder="12345" class="form-control" required>
</div>
<div class="form-group">
    <label for="phone">Business Phone Number</label>
    <input v-mask="'(###) ###-####'" v-model="phone" type="tel" name="phone" id="phone" placeholder="(111) 222-3333" class="form-control" required>
</div>
<div class="form-group">
    <label for="accountEmail">Business Email</label>
    <input type="email" name="accountEmail" id="accountEmail" value="{{ $user->email }}" class="form-control" required>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary pull-right">Next</button>
</div>