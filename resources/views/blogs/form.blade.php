<div class="form-group">
    <label for="author">Author:</label>
    <input type="text" name="author" id="author" class="form-control"
           value="{{ old('author') !== null ? old('author') : ((isset($blog) && $blog->author) ? $blog->author : '') }}" required>
</div>

<div class="form-group">
    <label for="description">Describe Yourself:</label>
    <input type="text" name="description" id="description" class="form-control"
           value="{{ old('description') !== null ? old('description') : ((isset($blog) && $blog->description) ? $blog->description : '') }}" required>
</div>

<div class="form-group">
    <label for="blog_title">Title:</label>
    <input type="text" name="blog_title" id="blog_title" class="form-control"
           value="{{ old('blog_title') !== null ? old('blog_title') : ((isset($blog) && $blog->blog_title) ? $blog->blog_title : '') }}" required>
</div>

<div class="form-group">
    <label for="blog_body">Body:</label>
    <textarea name="blog_body" id="blog_body" class="form-control" rows="10" required>{{ old('blog_body') !== null ? old('blog_body') : ((isset($blog) && $blog->blog_body) ? $blog->blog_body : '') }}</textarea>
</div>

<div class="blog_hero">
  <label for="blog_hero">Add Hero Photo</label>
  <input type="file" name="blog_hero" id="blog_hero" class="form-control" value="{{ old('blog_hero') !== null ? old('blog_hero') : ((isset($blog) && $blog->blog_hero_name) ? $blog->blog_hero_name : '') }}" required>
</div>

<div class="blog_profile">
  <label for="blog_profile">Add Profile Photo</label>
  <input type="file" name="blog_profile" id="blog_profile" class="form-control" value="{{ old('blog_profile') !== null ? old('blog_profile') : ((isset($blog) && $blog->blog_profile_name) ? $blog->blog_profile_name : '') }}" required>
</div>

<hr>

<div class="form-group">
    <button type="submit" class="btn btn-primary">{{ !isset($profile) ? 'Create' : 'Update' }} Profile</button>
</div>
