<div class="box-body">
    {{ csrf_field() }}

    <div class="form-group">
        <label for="name">Product name:</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ $product->name }}" required>
    </div>

    <div class="form-group">
        <label for="price">Price:</label>
        <input data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '$ ', 'placeholder': '0'" type="tel" name="price" id="price" value="{{ $product->price }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="description">Product description:</label>
        <input type="text" name="description" id="description" class="form-control" value="{{ $product->description }}">
        <p class="help-block">Optional</p>
    </div>

    <div class="form-group">
        <label for="category">Category</label>
        <input id="category" class="form-control" name="category"></input>
        <p class="help-block">Optional</p>
    </div>

    <div class="form-group">
        <label for="sku">SKU:</label>
        <input type="text" name="sku" id="sku" class="form-control" value="{{ $product->sku }}">
        <p class="help-block">Optional</p>
    </div>

    <div class="photo-input">
        <label for="photo">Add Photo</label>
        <input type="file" name="photo" id="photo" value="$product->product_photo_path">
        <p class="help-block">Optional</p>
    </div>
</div>

<div class="box-footer">
    <button type="submit" class="btn btn-success pull-right">Update</button>
</div>
