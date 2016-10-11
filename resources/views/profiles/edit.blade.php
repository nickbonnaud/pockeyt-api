@extends('layoutDashboard')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Your Business Profile
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Business Profile</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-body box-profile">
              @if(is_null($user->profile->hero))
              <a href="#" data-toggle="modal" data-target="#businessHeroModal">
                <img src="{{ asset('/images/defaultBackground.png') }}" class="business-hero-img img-responsive" alt="Business Hero Image">
              </a>
            @else
              <img src="{{$user->profile->hero->url }}" class="business-hero-img img-responsive" alt="Business Hero Image">
              <div class="title-space text-center">
                <form action="{{ route('profiles.photos', ['profiles' => $user->profile->id]) }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="type" value="hero">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="delete-logo-button"><b>Delete</b> Background</button>
                </form>
              </div>
            @endif
            @if(is_null($user->profile->logo))
                <a href="#" data-toggle="modal" data-target="#businessLogoModal">
                    <img src="{{ asset('/images/defaultLogo.png') }}" class="business-logo-img img-responsive" alt="Business Logo Image">
                </a>
            @else
                <img src="{{$user->profile->logo->url }}" class="business-logo-img img-responsive" alt="Business Logo Image">
                <div class="title-space-logo">
                    <form action="{{ route('profiles.photos', ['profiles' => $user->profile->id]) }}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="type" value="logo">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="delete-logo-button"><b>Delete</b> Logo</button>
                    </form>
                </div>
            @endif
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Business Name</b>
                        <p class="pull-right">{{ $user->profile->business_name }}</p>
                    </li>
                    <li class="list-group-item">
                        <b>Website</b>
                        <p class="pull-right">{{ $user->profile->website }}</p>
                    </li>
                    <li class="list-group-item">
                        <b>Business Description</b>
                        <p class="pull-right">{{ $user->profile->description }}</p>
                    </li>
                </ul>
                <a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#businessInfoModal">
                    <b>Edit</b>
                </a>
            </div>
          </div>
        @include ('errors.form')
        </div>
        <!-- Business Location -->
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Location used for payments</h3>
                    <div class="box-body">
                        <input id="pac-input" class="controls" type="text" placeholder="Enter a location">
                        <div id="map"></div>
                        <a href="#" class="btn btn-danger btn-block" data-toggle="modal" data-target="#businessLocationModal">
                        <b>Set Business to THIS Location</b>
                    </a>
                    </div>
                </div>
            </div>
        </div>
      <!-- Business Tags -->
        <div class="col-md-3">
            <div class="box box-primary collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Tags</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                   <ul class="list-group list-group-unbordered"">
                        @foreach ($user->profile->tags as $tag)
                            <li class="list-group-item business-tags">{{ $tag->name }}</li>
                        @endforeach
                    </ul>
                    <a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#businessTagsModal">
                        <b>Change</b>
                    </a>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
     <!--  Modals -->
        <div class="modal fade" id="businessHeroModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="userPhotoModalLabel">Change Background Image</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body">
                            <p><label>Click or Drag-n-Drop your Background Image Here</label></p>
                            <form id="uploadHero" action="{{ route('profiles.photos', ['profiles' => $user->profile->id]) }}" method="POST" class="dropzone">
                            {{ csrf_field() }}
                            <input type="hidden" name="type" value="hero">
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="businessLogoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="userPhotoModalLabel">Change Logo Image</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body">
                            <p><label>Click or Drag-n-Drop your Logo Photo Here</label></p>
                            <form id="uploadLogo" action="{{ route('profiles.photos', ['profiles' => $user->profile->id]) }}" method="POST" class="dropzone">
                            {{ csrf_field() }}
                            <input type="hidden" name="type" value="logo">
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="businessInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="userInfoModalLabel">Edit Info</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::model($profile, ['method' => 'PATCH', 'route' => ['profiles.update', $user->profile->id], 'class' => 'form-horizontal']) !!}
                            @include ('profiles.form_edit')
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="businessTagsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="businessTagsModalLabel">Change tags by typing and selecting new ones or deleting old ones</h4>
                    </div>
                    <div class="modal-body">
                       <div class="form-group">
                            {!! Form::model($profile, ['method' => 'PATCH', 'route' => ['profiles.tags', $user->profile->id], 'class' => 'form-horizontal']) !!}
                                {!! Form::label('tag_list', 'Tags:') !!}
                                {!! Form::select('tag_list[]', $tags, null, ['id' => 'tags', 'multiple', 'required']) !!}
                                <div class="modal-footer">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            {!! Form::close() !!}
                      </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="businessLocationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="businessTagsModalLabel"><b>Warning</b> this changes the location of your business, which impacts your ability to collect payments</h4>
                    </div>
                    <div class="modal-body">
                            {!! Form::model($profile, ['method' => 'PATCH', 'route' => ['profiles.location', $user->profile->id], 'class' => 'form-horizontal']) !!}
                                {!! Form::hidden('lat', null, ['id' => 'lat']) !!}
                                {!! Form::hidden('lng', null, ['id' => 'lng']) !!}
                                <div class="modal-footer">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        <!-- /. Modals section -->
    </section>
</div>
    <!-- /.content -->
  <!-- /.content-wrapper -->
@stop

@section('scripts.footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB5bWVb25GSXY-fhI5EFNJ8JualZcSluXE&libraries=places&callback=initMap"
        async defer></script>

    <script>
        function initMap() {
            var lat = {!! $profile->lat !!};
            var lng = {!! $profile->lng !!}
            var bizLatlng = new google.maps.LatLng(lat,lng);

            var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: lat, lng: lng},
            zoom: 17
            });

            var defaultMarker = new google.maps.Marker({
                position: bizLatlng,
            });

            defaultMarker.setMap(map);

            var input = document.getElementById('pac-input');

            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);

            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            var infowindow = new google.maps.InfoWindow();
            marker = new google.maps.Marker({
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

        };

        Dropzone.options.uploadHero = {
            paramName: 'photo',
            maxFilesize: 3,
            acceptedFiles: '.jpg, .jpeg, .png, .bmp',
            init: function() {
                this.on('success', function() {
                    window.location.reload();
                });
            }
        };

        Dropzone.options.uploadLogo = {
            paramName: 'photo',
            maxFilesize: 3,
            acceptedFiles: '.jpg, .jpeg, .png, .bmp',
            init: function() {
                this.on('success', function() {
                    window.location.reload();
                });
            }
        };

        $('#tags').select2({
            placeholder: 'Type 3 or less tags that describe your business',
            maximumSelectionLength: 3
        });

        $('#map').animate({height: '300'}, 1500 ,
                  function(){google.maps.event.trigger(map, 'resize');});
        $('#map').animate({height: '300'},
                         {progress:function()
                                   {google.maps.event.trigger(map, 'resize');},
                          duration:1500} );

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB5bWVb25GSXY-fhI5EFNJ8JualZcSluXE&libraries=places&callback=initMap"
        async defer></script>
@stop