<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{config('constants.site_title','Tranxit')}} - Trip</title>
    <link rel="shortcut icon" type="image/png" href="{{ config('constants.site_icon') }}"/>

    <link href="{{asset('asset/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/css/slick.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('asset/css/slick-theme.css')}}"/>
    <link href="{{asset('asset/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/css/bootstrap-timepicker.css')}}" rel="stylesheet">
    @if(Config::get('app.locale')=='ar')
    <link href="{{asset('asset/css/arabic_dashboard-style.css')}}" rel="stylesheet">
    @else
    <link href="{{ asset('asset/css/dashboard-style.css') }}" rel="stylesheet" type="text/css">
    @endif
    <link rel="stylesheet" href="{{ asset('main/assets/css/style_dialog.css')}}">
    @yield('styles')
</head>

<body>

    <div class="page-content dashboard-page" style="padding-top: 10px">    
        <div class="container">
            <h3>{{ substr_replace($ride->first_name,"***",-1) }}'s Trip</h3>
            <div class="col-md-12">
                <div class="dash-content">
                    <div class="row no-margin">
                        <div class="col-md-12">
                            <h4 class="page-title">@lang('user.ride.ride_now')</h4>
                        </div>
                    </div>
                    @include('common.notify')
                    <div class="row no-margin">
                        <div class="col-md-12">
                            <div id="map" style="width:100%;height:470px;"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@include('user.include.footer')
  
<script src="{{asset('main/vendor/jquery/jquery-1.11.3.min.js')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('constants.map_key')}}"></script>
    <script type="text/javascript">
      var map;
      var directionDisplay;
      var directionsService;
      var position;
      var marker = null;
      var polyline = null;
      var poly2 = null;
      var infowindow = null;
      var timerHandle = null;

      localStorage.clear();

      var meters, milliseconds, marker_url; 

      function initialize() {

        // Instantiate a directions service.
        directionsService = new google.maps.DirectionsService();

        // Create a map and center it on Manhattan.
        var myOptions = {
          zoom: 13,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(
          document.getElementById("map"),
          myOptions
        );

        map.setCenter({lat: parseFloat('{{$ride->lat}}'), lng: parseFloat('{{$ride->lng}}') });

        // Create a renderer for directions and bind it to the map.
        var rendererOptions = {
          map: map
        };
        directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);

        polyline = new google.maps.Polyline({
          path: [],
          strokeColor: "#FF0000",
          strokeWeight: 3
        });
        poly2 = new google.maps.Polyline({
          path: [],
          strokeColor: "#FF0000",
          strokeWeight: 3
        });

        track();

        setInterval(track, 5000);

      }



      function track() {
        $.ajax({
          type: "POST",
          url: "{{route('track')}}",
          data: {
            _token: '{{ csrf_token() }}',
            id: '{{$id}}'
          },
          success: function(response){

              meters = 4;//response.meters;
              milliseconds = 100;//((response.minutes * 60) * 1000); 
              marker_url = response.marker;

              if(typeof localStorage.starting_point == 'undefined') {
                localStorage.setItem('starting_point', response.source);
                localStorage.setItem('destination_point', response.destination);

                calcRoute(localStorage.starting_point, localStorage.destination_point);
              }

              if(localStorage.starting_point != response.source || localStorage.destination_point != response.destination) {

                localStorage.setItem('starting_point', response.source);
                localStorage.setItem('destination_point', response.destination);

                calcRoute(localStorage.starting_point, localStorage.destination_point);
              }

              
        }

      });
     }

      var steps = [];

      function calcRoute(origin, destination) {
        if (timerHandle) {
          clearTimeout(timerHandle);
        }
        if (marker) {
          marker.setMap(null);
        }
        polyline.setMap(null);
        poly2.setMap(null);
        directionsDisplay.setMap(null);
        polyline = new google.maps.Polyline({
          path: [],
          strokeColor: "#FF0000",
          strokeWeight: 3
        });
        poly2 = new google.maps.Polyline({
          path: [],
          strokeColor: "#FF0000",
          strokeWeight: 3
        });
        // Create a renderer for directions and bind it to the map.
        var rendererOptions = {
          map: map,
          suppressMarkers: true
        };
        directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);

        var travelMode = google.maps.DirectionsTravelMode.DRIVING;

        var request = {
          origin: origin,
          destination: destination,
          travelMode: travelMode
        };

        // Route the directions and pass the response to a
        // function to create markers for each step.
        directionsService.route(request, function(response, status) {
          if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);

            var bounds = new google.maps.LatLngBounds();
            var route = response.routes[0];
            startLocation = new Object();
            endLocation = new Object();

            // For each route, display summary information.
            var path = response.routes[0].overview_path;
            var legs = response.routes[0].legs;
            for (i = 0; i < legs.length; i++) {
              if (i === 0) {
                startLocation.latlng = legs[i].start_location;
                startLocation.address = legs[i].start_address;
              }

              var steps = legs[i].steps;
              for (j = 0; j < steps.length; j++) {
                var nextSegment = steps[j].path;
                for (k = 0; k < nextSegment.length; k++) {
                  polyline.getPath().push(nextSegment[k]);
                  bounds.extend(nextSegment[k]);
                }
              }
            }
            polyline.setMap(map);
            map.fitBounds(bounds);
            map.setZoom(18);
            startAnimation();
          }
        });
      }

      var eol;
      var k = 0;
      var stepnum = 0;
      var speed = "";
      var lastVertex = 1;

      //=============== animation functions ======================
      function updatePoly(d) {
        // Spawn a new polyline every 20 vertices, because updating a 100-vertex poly is too slow
        if (poly2.getPath().getLength() > 20) {
          poly2 = new google.maps.Polyline([
            polyline.getPath().getAt(lastVertex - 1)
          ]);
        }

        if (polyline.GetIndexAtDistance(d) < lastVertex + 2) {
          if (poly2.getPath().getLength() > 1) {
            poly2.getPath().removeAt(poly2.getPath().getLength() - 1);
          }
          poly2
            .getPath()
            .insertAt(
              poly2.getPath().getLength(),
              polyline.GetPointAtDistance(d)
            );
        } else {
          poly2
            .getPath()
            .insertAt(poly2.getPath().getLength(), endLocation.latlng);
        }
      }

      function animate(d) {
        if (d > eol) {
          map.panTo(endLocation.latlng);
          marker.setPosition(endLocation.latlng);
          return;
        }
        var p = polyline.GetPointAtDistance(d);
        map.panTo(p);
        var lastPosn = marker.getPosition();
        marker.setPosition(p);
        var heading = google.maps.geometry.spherical.computeHeading(
          lastPosn,
          p
        );
        icon.rotation = heading;
        marker.setIcon(icon);
        updatePoly(d);
        timerHandle = setTimeout("animate(" + (d + meters) + ")", milliseconds);
      }

      function startAnimation() {
        eol = polyline.Distance();
        map.setCenter(polyline.getPath().getAt(0));
        marker = new google.maps.Marker({
          position: polyline.getPath().getAt(0),
          map: map,
          icon: icon
        });

        poly2 = new google.maps.Polyline({
          path: [polyline.getPath().getAt(0)],
          strokeColor: "#0000FF",
          strokeWeight: 10
        });
        // map.addOverlay(poly2);
        setTimeout("animate(50)", 2000); // Allow time for the initial map display
      }
      google.maps.event.addDomListener(window, "load", initialize);

      var car =
        "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z"; 

      var icon = {
       path: car,
        scale: 0.7,
        strokeColor: "white",
        strokeWeight: 0.1,
        fillOpacity: 1,
        fillColor: "#404040",
        offset: "5%",
        //url: marker_url,
        anchor: new google.maps.Point(10, 25) // orig 10,50 back of car, 10,0 front of car, 10,25 center of car
      };

      // === first support methods that don't (yet) exist in v3
      google.maps.LatLng.prototype.distanceFrom = function(newLatLng) {
        var EarthRadiusMeters = 6378137.0; // meters
        var lat1 = this.lat();
        var lon1 = this.lng();
        var lat2 = newLatLng.lat();
        var lon2 = newLatLng.lng();
        var dLat = ((lat2 - lat1) * Math.PI) / 180;
        var dLon = ((lon2 - lon1) * Math.PI) / 180;
        var a =
          Math.sin(dLat / 2) * Math.sin(dLat / 2) +
          Math.cos((lat1 * Math.PI) / 180) *
            Math.cos((lat2 * Math.PI) / 180) *
            Math.sin(dLon / 2) *
            Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = EarthRadiusMeters * c;
        return d;
      };

      google.maps.LatLng.prototype.latRadians = function() {
        return (this.lat() * Math.PI) / 180;
      };

      google.maps.LatLng.prototype.lngRadians = function() {
        return (this.lng() * Math.PI) / 180;
      };

      // === A method which returns the length of a path in metres ===
      google.maps.Polygon.prototype.Distance = function() {
        var dist = 0;
        for (var i = 1; i < this.getPath().getLength(); i++) {
          dist += this.getPath()
            .getAt(i)
            .distanceFrom(this.getPath().getAt(i - 1));
        }
        return dist;
      };

      // === A method which returns a GLatLng of a point a given distance along the path ===
      // === Returns null if the path is shorter than the specified distance ===
      google.maps.Polygon.prototype.GetPointAtDistance = function(metres) {
        // some awkward special cases
        if (metres == 0) return this.getPath().getAt(0);
        if (metres < 0) return null;
        if (this.getPath().getLength() < 2) return null;
        var dist = 0;
        var olddist = 0;
        for (var i = 1; i < this.getPath().getLength() && dist < metres; i++) {
          olddist = dist;
          dist += this.getPath()
            .getAt(i)
            .distanceFrom(this.getPath().getAt(i - 1));
        }
        if (dist < metres) {
          return null;
        }
        var p1 = this.getPath().getAt(i - 2);
        var p2 = this.getPath().getAt(i - 1);
        var m = (metres - olddist) / (dist - olddist);
        return new google.maps.LatLng(
          p1.lat() + (p2.lat() - p1.lat()) * m,
          p1.lng() + (p2.lng() - p1.lng()) * m
        );
      };

      // === A method which returns an array of GLatLngs of points a given interval along the path ===
      google.maps.Polygon.prototype.GetPointsAtDistance = function(metres) {
        var next = metres;
        var points = [];
        // some awkward special cases
        if (metres <= 0) return points;
        var dist = 0;
        var olddist = 0;
        for (var i = 1; i < this.getPath().getLength(); i++) {
          olddist = dist;
          dist += this.getPath()
            .getAt(i)
            .distanceFrom(this.getPath().getAt(i - 1));
          while (dist > next) {
            var p1 = this.getPath().getAt(i - 1);
            var p2 = this.getPath().getAt(i);
            var m = (next - olddist) / (dist - olddist);
            points.push(
              new google.maps.LatLng(
                p1.lat() + (p2.lat() - p1.lat()) * m,
                p1.lng() + (p2.lng() - p1.lng()) * m
              )
            );
            next += metres;
          }
        }
        return points;
      };

      // === A method which returns the Vertex number at a given distance along the path ===
      // === Returns null if the path is shorter than the specified distance ===
      google.maps.Polygon.prototype.GetIndexAtDistance = function(metres) {
        // some awkward special cases
        if (metres == 0) return this.getPath().getAt(0);
        if (metres < 0) return null;
        var dist = 0;
        var olddist = 0;
        for (var i = 1; i < this.getPath().getLength() && dist < metres; i++) {
          olddist = dist;
          dist += this.getPath()
            .getAt(i)
            .distanceFrom(this.getPath().getAt(i - 1));
        }
        if (dist < metres) {
          return null;
        }
        return i;
      };
      // === Copy all the above functions to GPolyline ===
      google.maps.Polyline.prototype.Distance =
        google.maps.Polygon.prototype.Distance;
      google.maps.Polyline.prototype.GetPointAtDistance =
        google.maps.Polygon.prototype.GetPointAtDistance;
      google.maps.Polyline.prototype.GetPointsAtDistance =
        google.maps.Polygon.prototype.GetPointsAtDistance;
      google.maps.Polyline.prototype.GetIndexAtDistance =
        google.maps.Polygon.prototype.GetIndexAtDistance;
    </script>
</body>
</html>