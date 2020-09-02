@extends('admin.layout.base')

@section('title', 'Dashboard ')

@section('content')
<?php $diff = ['-success','-info','-warning','-danger']; ?>

<div class="content-area py-1">
<div class="container-fluid">
		<div class="box box-block bg-white">
				<div class="clearfix mb-1">
					<h5 class="float-xs-left">@lang('admin.heatmap.Ride_Heatmap')</h5>
					<div class="float-xs-right">
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div id="floating-panel">
      <button class="btn btn-primary" onclick="toggleHeatmap(event)">Toggle Heatmap</button>
      <button class="btn" onclick="changeGradient(event)">Change gradient</button>
      <button class="btn btn-primary" onclick="changeRadius(event)">Change radius</button>
      <button class="btn btn-primary" onclick="changeOpacity(event)">Change opacity</button>
    </div> <br>

						<div id="map" style="width:100%;height:530px;background:#ccc"></div>
					</div>
				</div>
			</div>
	</div>
</div>
@endsection

@section('scripts')
	<script async src="https://maps.googleapis.com/maps/api/js?key={{config('constants.map_key')}}&libraries=visualization"></script>
	<script type="text/javascript" src="{{asset('main/vendor/heatmap/heatmap.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('main/vendor/heatmap/gmaps-heatmap.js')}}"></script>
	<script type="text/javascript">
		
	  var map, heatmap;

	  if( navigator.geolocation ) {
         navigator.geolocation.getCurrentPosition( success, fail );
      } else {
          console.log('Sorry, your browser does not support geolocation services');
          initMap();
      }

      function success(position)
      {

        if(position.coords.longitude != "" && position.coords.latitude != ""){
            current_longitude = position.coords.longitude;
            current_latitude = position.coords.latitude;
        }

        initMap(current_latitude, current_longitude);
      }

      function fail()
      {
        initMap();
      }

      function initMap(latitude = 0, longitude = 0) {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 13,
          center: {lat: latitude, lng: longitude},
          mapTypeId: 'roadmap'
        });

        heatmap = new google.maps.visualization.HeatmapLayer({
          map: map,
          radius: 30
        });

        getData();
      }

      setInterval(getData, 8000);

      function toggleHeatmap(event) {
        heatmap.setMap(heatmap.getMap() ? null : map);
        toggleClasses(event);
      }

      function changeGradient(event) {
        var gradient = [
          'rgba(0, 255, 255, 0)',
          'rgba(0, 255, 255, 1)',
          'rgba(0, 191, 255, 1)',
          'rgba(0, 127, 255, 1)',
          'rgba(0, 63, 255, 1)',
          'rgba(0, 0, 255, 1)',
          'rgba(0, 0, 223, 1)',
          'rgba(0, 0, 191, 1)',
          'rgba(0, 0, 159, 1)',
          'rgba(0, 0, 127, 1)',
          'rgba(63, 0, 91, 1)',
          'rgba(127, 0, 63, 1)',
          'rgba(191, 0, 31, 1)',
          'rgba(255, 0, 0, 1)'
        ]
        heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
        toggleClasses(event);
      }

      function changeRadius(event) {
        heatmap.set('radius', heatmap.get('radius') ? null : 30);
        toggleClasses(event);
      }

      function changeOpacity(event) {
        heatmap.set('opacity', heatmap.get('opacity') ? null : 0.2);
        toggleClasses(event);
      }

      function toggleClasses(event) {
        if($(event.target).hasClass('btn-primary')) {
        	$(event.target).removeClass('btn-primary');
        } else {
        	$(event.target).addClass('btn-primary');
        }
      }

      function getData() {

        $.ajax({
			url: "{{ route('admin.get_heatmap') }}",
			type: 'get',
			data: {
			},
			success:function(data, textStatus, jqXHR) {
				var points = [];

				for(var datum of data) {
					points.push(new google.maps.LatLng(datum.lat, datum.lng));
				}
				heatmap.setData(points);
			}
		});
      }

	</script>

@endsection