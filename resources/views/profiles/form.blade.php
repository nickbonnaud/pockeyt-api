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

<div class="form-group">
    <label for="website">Review URL:</label>
    <input type="text" name="review_url" id="website" class="form-control"
           value="{{ old('review_url') !== null ? old('review_url') : ((isset($profile) && $profile->review_url) ? $profile->review_url : '') }}" >
</div>

<div class="form-group">
    <label for="review_intro">Review Intro:</label>
    <input type="text" name="review_intro" id="review_intro" class="form-control"
           value="{{ old('review_intro') !== null ? old('review_intro') : ((isset($profile) && $profile->review_intro) ? $profile->review_intro : '') }}" >
</div>


@if(isset($profile))
  <div class="form-group">
      {!! Form::label('tag_list', 'Tag:') !!}
      {!! Form::select('tag_list[]', $tags, null, ['id' => 'tags', 'class' => 'form-control', 'multiple', 'required']) !!}
  </div>
@else
  <div class="form-group">
      {!! Form::label('tags', 'Tag:') !!}
      {!! Form::select('tags[]', $tags, null, ['id' => 'tags', 'class' => 'form-control', 'multiple']) !!}
  </div>
@endif

<hr>

<div class="form-group">
    <button type="submit" class="btn btn-primary">{{ !isset($profile) ? 'Create' : 'Update' }} Profile</button>
</div>

@section('scripts.footer')
  <script>
    $('#tags').select2({
        placeholder: 'Type 3 or less tags that describe your business',
        maximumSelectionLength: 3
    });
  </script>
@endsection
