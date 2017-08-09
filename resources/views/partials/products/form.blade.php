{{ csrf_field() }}
<div class="form-group">
    <label for="name">Product name:</label>
    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
</div>

<div class="form-group">
    <label for="price">Price:</label>
    <div class="input-group">
        <span class="input-group-addon">$</span>
        <input pattern="^\$?(([1-9](\d*|\d{0,2}(,\d{3})*))|0)(\.\d{1,2})?$" type="tel" name="price" id="price" class="form-control" required>
    </div>
</div>

<div class="form-group">
    <label for="description">Product description:</label>
    <input type="text" name="description" id="description" class="form-control" value="{{ old('description') }}">
    <p class="help-block">Optional</p>
</div>

<div class="form-group">
    <label for="category">Category</label>
    <select id="category" class="form-control" name="category" multiple="multiple"></select>
    <p class="help-block">Optional</p>
</div>

<div class="form-group">
    <label for="sku">SKU:</label>
    <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku') }}">
    <p class="help-block">Optional</p>
</div>

<div class="photo-input">
    <label for="photo">Add Photo</label>
    <input type="file" name="photo" id="photo">
    <p class="help-block">Optional</p>
</div>

<div class="modal-footer modal-footer-form-tags">
    <div class="form-group">
        <button type="submit" class="btn btn-success btn-form-footer">Add product</button>
    </div>
</div>
