
    <div class="box-body">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="title">Post title:</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        <div class="form-group">
            <label for="body">Message:</label>
            <textarea type="text" name="body" id="body" class="form-control" rows="10" required></textarea>
        </div>

        <div class="photo-input">
            <label for="photo">Add Photo</label>
            <input type="file" name="photo" id="photo">
            <p class="help-block">Optional photo</p>
        </div>

        <!-- <div class="form-group">
            <label for="event_date_pretty">Event Date (optional)</label>
            <input type="text" id="event_date_pretty">
        </div>

        <input type="hidden" id="event_date" name="event_date"> -->
    </div>

    <div class="box-footer">
        <button type="submit" class="btn btn-primary">Create Your Post!</button>
    </div>
