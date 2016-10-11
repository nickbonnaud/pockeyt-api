<div class="form-group">
    <label for="business_name">Business Name:</label>
    <input type="text" name="business_name" id="business_name" class="form-control"
           value="{{ old('business_name') !== null ? old('business_name') : ((isset($profile) && $profile->business_name) ? $profile->business_name : '') }}" required>
</div>

<div class="form-group">
    <label for="website">Website URL:</label>
    <input type="text" name="website" id="website" class="form-control"
           value="{{ old('website') !== null ? old('website') : ((isset($profile) && $profile->website) ? $profile->website : '') }}" placeholder="www.example.com" required>
</div>

<div class="form-group">
    <label for="description">Business Description:</label>
    <textarea name="description" id="description" class="form-control" rows="10" required>{{ old('description') !== null ? old('description') : ((isset($profile) && $profile->description) ? $profile->description : '') }}</textarea>
</div>

<div class="form-group">
  <input id="pac-input" class="controls" type="text"
      placeholder="Enter a location">
  <div id="map"></div>
</div>

{!! Form::hidden('lat', null, ['id' => 'lat']) !!}
{!! Form::hidden('lng', null, ['id' => 'lng']) !!}


@if(isset($profile))
  <div class="form-group">
      {!! Form::label('tag_list', 'Tag:') !!}
      {!! Form::select('tag_list[]', $tags, null, ['id' => 'tags', 'class' => 'form-control', 'required' => 'required', 'multiple']) !!}
  </div>
@else
  <div class="form-group">
      {!! Form::label('tags', 'Tag:') !!}
      {!! Form::select('tags[]', $tags, null, ['id' => 'tags', 'class' => 'form-control', 'required' => 'required', 'multiple']) !!}
  </div>
@endif

<hr>

<div class="form-group">
    <button type="submit" class="btn btn-info pull-right">{{ !isset($profile) ? 'Next' : 'Update' }}</button>
</div>

@section('scripts.footer')
  <script>
    function initMap() {
      var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 35.7796, lng: -78.6382},
        zoom: 13
      });

      var input = document.getElementById('pac-input');

      var autocomplete = new google.maps.places.Autocomplete(input);
      autocomplete.bindTo('bounds', map);

      map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

      var infowindow = new google.maps.InfoWindow();
      var marker = new google.maps.Marker({
        map: map
      });
      marker.addListener('click', function() {
        infowindow.open(map, marker);
      });

      autocomplete.addListener('place_changed', function() {
        infowindow.close();
        var place = autocomplete.getPlace();
        if (!place.geometry) {
          return;
        }

        if (place.geometry.viewport) {
          map.fitBounds(place.geometry.viewport);
        } else {
          map.setCenter(place.geometry.location);
          map.setZoom(17);
        }

        // Set the position of the marker using the place ID and location.
        marker.setPlace({
          placeId: place.place_id,
          location: place.geometry.location
        });
        marker.setVisible(true);

        var latitude = place.geometry.location.lat();
        var longitude = place.geometry.location.lng();

        $('#lat').val(latitude);
        $('#lng').val(longitude);

        infowindow.setContent('<div><strong>' + place.name + '</strong><br>' +
            place.formatted_address);
        infowindow.open(map, marker);
      });
    }
    $('#tags').select2({
        placeholder: 'Type 3 or less tags that describe your business',
        maximumSelectionLength: 3
    });
  </script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB5bWVb25GSXY-fhI5EFNJ8JualZcSluXE&libraries=places&callback=initMap"
        async defer></script>
@endsection
