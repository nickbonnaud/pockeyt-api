<div class="box-body">
  <div class="form-group">
    <label for="business_name" class="col-sm-2 control-label">Business name</label>
    <div class="col-sm-10">
      <input type="text" name="business_name" class="form-control" id="business_name" value="{{ $user->profile->business_name }}" required="">
    </div>
  </div>
  <div class="form-group">
    <label for="website" class="col-sm-2 control-label">Website URL</label>
    <div class="col-sm-10">
      <input type="text" name="website" class="form-control" id="website" value="{{ $user->profile->website }}" required>
    </div>
  </div>
  <div class="form-group">
    <label for="description" class="col-sm-2 control-label">Business Description</label>
    <div class="col-sm-10">
      <textarea type="text" name="description" class="form-control" id="description" value="{{ $user->profile->description }}" required></textarea>
    </div>
  </div>
  <div class="modal-footer">
    <div class="form-group">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
  </div>
</div>