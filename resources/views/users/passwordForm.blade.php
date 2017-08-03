<div class="form-group">
	<label for="old_password" class="col-sm-2 control-label">Current Password</label>
	<div class="col-sm-10">
		<input type="password" name="old_password" class="form-control" id="old_password" required>
	</div>
</div>
<div class="form-group">
	<label for="new_password" class="col-sm-2 control-label">New Password</label>
	<div class="col-sm-10">
		<input class="form-control" 
			v-validate="{
        rules: 
            { 
              regex: /^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%=@&?]).*$/,
              required: true,
              confirmed: 'password_confirm',
              min: 9,
              max: 72, 
            }
        }"
      name="new_password" type="password" :class="{'input': true, 'is-danger': errors.has('new_password') }" required>
      <p class="form-text text-muted">Password must be at least 9 characters long and contain 3 of the 4 categories: uppercase, lowercase, numbers, special characters.</p>
	</div>
</div>
<div class="form-group">
	<label for="password_confirm" class="col-sm-2 control-label">Confirm Password</label>
	<div class="col-sm-10">
		<input class="form-control" :class="{'input': true, 'is-danger': errors.has('new_password') }" name="password_confirm" type="password" required>
	</div>
</div>
<span v-show="errors.has('new_password')" class="help is-danger">@{{ errors.first('new_password') }}</span>
<div class="modal-footer modal-footer-form-tags">
	<div class="form-group">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="submit" :disabled="errors.has('new_password')" class="btn btn-primary btn-form-footer">Save changes</button>
	</div>
</div>