{{ csrf_field() }}

<div class="form-group">
    <label for="business_name">Business Name:</label>
    <input type="text" name="business_name" id="business_name" class="form-control"
           value="{{ old('business_name') !== null ? old('business_name') : ((isset($profile) && $profile->business_name) ? $profile->business_name : '') }}" required>
</div>

<div class="form-group">
    <label for="website">Website URL:</label>
    <input type="text" name="website" id="website" class="form-control"
           value="{{ old('website') !== null ? old('website') : ((isset($profile) && $profile->website) ? $profile->website : '') }}" required>
</div>

<div class="form-group">
    <label for="description">Business Description:</label>
    <textarea name="description" id="description" class="form-control" rows="10" required>{{ old('description') !== null ? old('description') : ((isset($profile) && $profile->description) ? $profile->description : '') }}</textarea>
</div>

<hr>

<div class="form-group">
    <button type="submit" class="btn btn-primary">{{ !isset($profile) ? 'Create' : 'Update' }} Profile</button>
</div>
