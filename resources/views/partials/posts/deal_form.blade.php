<div class="box-body">
    {{ csrf_field() }}
   <div class="form-group">
      <label for="message">Message:</label>
      <textarea type="text" name="message" id="message" class="form-control" rows="10" required></textarea>
    </div>
    <div class="form-group">
    	<label for="price">Price of Deal</label>
    	<span class="input-group-addon">$</span>
    	<input class="form-control" type="number" name="price" id="price" pattern="^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$" step="any" placeholder="25.00" required>
    </div>
    <div class="photo-input">
        <label for="photo">Add Photo</label>
        <input type="file" name="photo" id="photo" required>
        <p class="help-block">Required photo</p>
    </div>

    <div class="form-group">
        <label>Last day of deal:</label>
        <div class="input-group date">
        	<div class="input-group-addon">
        		<i class="fa fa-calendar"></i>
        	</div>
        	<input type="text" class="form-control pull-right" id="event_date_pretty">
        </div>
    </div>

    <input type="hidden" id="end_date" name="event_date" required>
</div>

<div class="box-footer">
    <button type="submit" class="btn btn-primary">Create Your Event!</button>
</div>
