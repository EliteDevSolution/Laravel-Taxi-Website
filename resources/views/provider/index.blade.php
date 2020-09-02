@extends('provider.layout.app')

@section('content')

@if(Auth::user()->status=='document')
<div class="pro-dashboard-head">
    <div class="container">
        <a href="#" class="pro-head-link active">@lang('provider.document_pending')</a>
    </div>
</div>
<div class="pro-dashboard-content">
    <div class="container">
        <div class="dash-content">
            <div class="row no-margin">
                <div className="col-md-12">  
                    <form method="POST" action="provider/profile/available">
                        <div class="offline">
                                <img src="/asset/img/offline.gif"/>
                            </div>                  
                        <a href="{{ route('provider.documents.index') }}" class="full-primary-btn">@lang('provider.document_upload')</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@elseif(Auth::user()->status=='card')
<div class="pro-dashboard-head">
    <div class="container">
        <a href="#" class="pro-head-link active">@lang('provider.card_pending')</a>
    </div>
</div>
<div class="pro-dashboard-content">
    <div class="container">
        <div class="dash-content">
            <div class="row no-margin">
                <div className="col-md-12">  
                    <form method="POST" action="provider/profile/available">
                        <div class="offline">
                                <img src="/asset/img/offline.gif"/>
                            </div>                  
                        <a href="{{ route('provider.cards') }}" class="full-primary-btn">@lang('provider.card.add_debit_card')</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@elseif(Auth::user()->status=='balance')
<div class="pro-dashboard-head">
    <div class="container">
        <a href="#" class="pro-head-link active">@lang('provider.negative_balance')</a>
    </div>
</div>
<div class="pro-dashboard-content">
    <div class="container">
        <div class="dash-content">
            <div class="row no-margin">
                <div className="col-md-12">  
                    <form method="POST" action="provider/profile/available">
                        <div class="offline">
                                <img src="/asset/img/offline.gif"/>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@else
<div id="msgs"></div>
<div class="pro-dashboard-head">
    <div class="container">
        <div class="alert alert-success" style="display: none;" id="show_cancelled">
            <button type="button" class="close" data-dismiss="alert">×</button>
            Ride Cancelled
        </div>
        <a href="#" class="pro-head-link active">@lang('provider.drive_now')</a>
    </div>
</div>
<div class="pro-dashboard-content">
    <div class="container">
        <div class="dash-content" id="trip-container">
            <div class="row no-margin" >

            </div>
        </div>
    </div>
</div>
<style type="text/css">
    .reasonvalidate{
        display: none;
    }
    .cancel_hide{
        display: none;
    }
    .cancel_show{
        display: inline-block;
    }
    .price{
        width: 100px;
        float: right;
    }
</style>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).on('click', '#cancel_reason', function(){
        //console.log($(this).val());
        if($(this).val()=='ot'){
            $("#cancel_text").removeClass('cancel_hide');
            $("#cancel_text").attr('required', true);
            //$("#cancel_text").addClass('cancel_show');
        }
        else{
            $("#cancel_text").attr('required', false);
            $("#cancel_text").addClass('cancel_hide');
            //$("#cancel_text").addClass('cancel_show');
        }
    });

    $(document).on('click', '#waiting_div', function(){
        
        var isChecked = $("#waiting_time").is(":checked");
        var request_id = $(this).attr("data-id");
        var test_status = $(this).attr("data-status");
        if(test_status!=1){
                $.ajax({
                url: "{{ url('/provider/waiting') }}",
                type: 'post',
                data: {
                    _token : '{{ csrf_token() }}',
                    id:request_id,
                    status:isChecked,               
                },
                success:function(data) {
                    //console.log(data);
                }
            });
        }
        else{
            $(this).attr("data-status",0);
        }
        

    });   
    
    function check_waiting(request_id){        
        $.ajax({
            url: "{{ url('/provider/waiting') }}",
            type: 'post',
            data: {
                _token : '{{ csrf_token() }}',
                id:request_id  
            },
            success:function(data) {                
                if(data.waitingStatus==1){
                    toggleSwitch("#waiting_time", true);                    
                }else{
                    toggleSwitch("#waiting_time", false);
                }

            }
        });
    } 

    function toggleSwitch(switch_elem, on) {
    if (on){ // turn it on
        if ($(switch_elem)[0].checked){ // it already is so do 
            // nothing
        }else{
            $(switch_elem).trigger('click').attr("checked", "checked"); // it was off, turn it on
        }
    }else{ // turn it off
        if ($(switch_elem)[0].checked){ // it's already on so 
            $(switch_elem).trigger('click').removeAttr("checked"); // turn it off
        }else{ // otherwise 
            // nothing, already off
        }
    }
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ Config::get('constants.map_key') }}&libraries=places&language=@lang('provider.lang')" defer></script>
<script type="text/javascript">
    var online="@lang('provider.online')";
    var offline="@lang('provider.offline')";
    var map;
    var request=0;
    var currency="{{config('constants.currency')}}";
    var routeMarkers = {
                source: {
                    lat: 0,
                    lng: 0,
                },
                destination: {
                    lat: 0,
                    lng: 0,
                }
            };
    var zoomLevel = 13;
    var directionsService;
    var directionsDisplay;
    if(localStorage.getItem("cancelled")==1){
        $("#show_cancelled").show();
        localStorage.setItem("cancelled", 0);
    }
    function initMap() {
        // Basic options for a simple Google Map
        var center = new google.maps.LatLng('13', '80');
        
        directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer;
        // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions

        var mapOptions = {
            // How zoomed in you want the map to start at (always required)
            zoom: zoomLevel,
            disableDefaultUI: true,
            // The latitude and longitude to center the map (always required)
            center: center,

            // Map styling
            styles: [
                {
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#f5f5f5"
                        }
                    ]
                },
                {
                    elementType:"labels.icon",
                    stylers:[
                        {
                            visibility:"off"
                        }
                    ]
                },
                {
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#616161"
                        }
                    ]
                },
                {
                    elementType:"labels.text.stroke",
                    stylers:[
                        {
                            color:"#f5f5f5"
                        }
                    ]
                },
                {
                    featureType:"administrative.land_parcel",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#bdbdbd"
                        }
                    ]
                },
                {
                    featureType:"poi",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#eeeeee"
                        }
                    ]
                },
                {
                    featureType:"poi",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#757575"
                        }
                    ]
                },
                {
                    featureType:"poi.park",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#e5e5e5"
                        }
                    ]
                },
                {
                    featureType:"poi.park",
                    elementType:"geometry.fill",
                    stylers:[
                        {
                            color:"#7de843"
                        }
                    ]
                },
                {
                    featureType:"poi.park",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#9e9e9e"
                        }
                    ]
                },
                {
                    featureType:"road",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#ffffff"
                        }
                    ]
                },
                {
                    featureType:"road.arterial",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#757575"
                        }
                    ]
                },
                {
                    featureType:"road.highway",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#dadada"
                        }
                    ]
                },
                {
                    featureType:"road.highway",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#616161"
                        }
                    ]
                },
                {
                    featureType:"road.local",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#9e9e9e"
                        }
                    ]
                },
                {
                    featureType:"transit.line",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#e5e5e5"
                        }
                    ]
                },
                {
                    featureType:"transit.station",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#eeeeee"
                        }
                    ]
                },
                {
                    featureType:"water",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#c9c9c9"
                        }
                    ]
                },
                {
                    featureType:"water",
                    elementType:"geometry.fill",
                    stylers:[
                        {
                            color:"#9bd0e8"
                        }
                    ]
                },
                {
                    featureType:"water",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#9e9e9e"
                        }
                    ]
                }
            ]
        };

        // Get the HTML DOM element that will contain your map 
        // We are using a div with id="map" seen below in the <body>
        var mapElement = document.getElementById('map');

        // Create the Google Map using out element and options defined above
        map = new google.maps.Map(mapElement, mapOptions);

        navigator.geolocation.getCurrentPosition(function (position) { 
            center = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            map.setCenter(center);

            var marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29),
            });

            marker.setPosition(center);
            marker.setVisible(true);
        });

    }

    function updateMap(route) {

        console.log('updateMap', route, routeMarkers);
        // var markerSecond = new google.maps.Marker({
        //     map: map,
        //     anchorPoint: new google.maps.Point(0, -29)
        // });

        // source = new google.maps.LatLng('13', '80');
        // destination = new google.maps.LatLng('13', '80');

        // marker.setVisible(false);
        // marker.setPosition(source);

        // markerSecond.setVisible(false);
        // markerSecond.setPosition(destination);

        // var bounds = new google.maps.LatLngBounds();
        // bounds.extend(marker.getPosition());
        // bounds.extend(markerSecond.getPosition());
        // map.fitBounds(bounds);

        if(routeMarkers.source.lat == route.source.lat &&
            routeMarkers.source.lng == route.source.lng &&
            routeMarkers.destination.lat == route.destination.lat &&
            routeMarkers.destination.lng == route.destination.lng) {

        } else {
            
            routeMarkers = route;
            console.log(routeMarkers);
            directionsDisplay.set('directions', null);
            directionsDisplay.setMap(map);

            directionsService.route({
                origin: route.source,
                destination: route.destination,
                travelMode: google.maps.TravelMode.DRIVING
            }, function(result, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(result);
                }
            });
        }

    }


/*var _registration = null;
function registerServiceWorker() {
  return navigator.serviceWorker.register("{{ asset('js/service-worker.js') }}")
  .then(function(registration) {
    console.log('Service worker successfully registered.');
    _registration = registration;
    return registration;
  })
  .catch(function(err) {
    console.error('Unable to register service worker.', err);
  });
}

function askPermission() {
  return new Promise(function(resolve, reject) {
    const permissionResult = Notification.requestPermission(function(result) {
      resolve(result);
    });

    if (permissionResult) {
      permissionResult.then(resolve, reject);
    }
  })
  .then(function(permissionResult) {
    if (permissionResult !== 'granted') {
      throw new Error('We weren\'t granted permission.');
    }
    else{
      subscribeUserToPush();
    }
  });
}

function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/\-/g, '+')
    .replace(/_/g, '/');

  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

function getSWRegistration(){
  var promise = new Promise(function(resolve, reject) {
  // do a thing, possibly async, then…

  if (_registration != null) {
    resolve(_registration);
  }
  else {
    reject(Error("It broke"));
  }
  });
  return promise;
}

function subscribeUserToPush() {
  getSWRegistration()
  .then(function(registration) {
    console.log(registration);
    const subscribeOptions = {
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(
        "{{env('VAPID_PUBLIC_KEY')}}"
      )
    };

    return registration.pushManager.subscribe(subscribeOptions);
  })
  .then(function(pushSubscription) {
    console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
    sendSubscriptionToBackEnd(pushSubscription);
    return pushSubscription;
  });
}

function sendSubscriptionToBackEnd(subscription) {
    $.ajax({
            url: "/save-subscription/{{Auth::user()->id}}/provider",
            headers: {'Content-Type': 'application/json'},
            type: 'post',
            data: JSON.stringify(subscription),
            success:function(data, textStatus, jqXHR) {
                console.log(data);
            }
        });
}

  askPermission();

  registerServiceWorker();*/

</script>
@endif
@endsection