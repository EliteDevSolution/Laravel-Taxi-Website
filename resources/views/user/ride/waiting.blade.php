@extends('user.layout.base')

@section('title', 'On Ride')

@section('content')

<div class="col-md-9">
    <div class="dash-content">
    	@include('common.notify')
		<div class="row no-margin">
		    <div class="col-md-12">
		        <h4 class="page-title" id="ride_status"></h4>
		    </div>
		</div>
		
		<div class="row no-margin">
		        <div class="col-md-6" id="container" >
		    		<p>Loading...</p>                             
		        </div>

		        <div class="col-md-6">
		            <dl class="dl-horizontal left-right">
		                <dt>@lang('user.request_id')</dt>
		                <dd>{{$request->id}}</dd>
		                <dt>@lang('user.time')</dt>
		                <dd>{{date('d-m-Y H:i A',strtotime($request->assigned_at))}}</dd>
		            </dl> 
		            <div class="user-request-map">

		                <div class="from-to row no-margin">
		                    <div class="from">
		                        <h5>@lang('user.from')</h5>
		                        <p>{{$request->s_address}}</p>
		                    </div>
		                    <div class="to">
		                        <h5>@lang('user.to') <span id="show_location" style="float: right;font-size:14px; display: none;"><a href="#" data-toggle="modal" data-target="#change-location"><span class="fa fa-edit"></span></a></span></h5>
		                        <p>{{$request->d_address}}</p>
		                    </div>
		                    <div class="type">
		                    	<h5>@lang('user.type')</h5>
		                        <p>{{$request->service_type->name}}</p>
		                    </div>
		                </div>
		                <?php 
		                    $map_icon = asset('asset/img/marker-start.png');
		                    $static_map = "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=600x450&maptype=roadmap&format=png&visual_refresh=true&markers=icon:".$map_icon."%7C".$request->s_latitude.",".$request->s_longitude."&markers=icon:".$map_icon."%7C".$request->d_latitude.",".$request->d_longitude."&path=color:0x191919|weight:8|enc:".$request->route_key."&key=".Config::get('constants.map_key'); ?>

		                    <div class="map-image">
		                    	<img src="{{$static_map}}">
		                    </div>                               
		            </div>                          
		        </div>
		</div>
	</div>

	<div id="change-location" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Change Location</h4>
				</div>
				<div class="modal-body">
					<form action="{{url('confirm/ride')}}" method="GET" onkeypress="return disableEnterKey(event);">
					 <input type="text" class="form-control" id="origin-input" name="s_address"  placeholder="Enter pickup location" value="" disabled="">
					 <input type="text" class="form-control" id="destination-input" name="d_address"  placeholder="Enter drop location" >
			        <input type="hidden" name="s_latitude" id="origin_latitude" value="{{$request->s_latitude}}">
                    <input type="hidden" name="s_longitude" id="origin_longitude" value="{{$request->s_longitude}}">
                    <input type="hidden" name="d_latitude" id="destination_latitude">
                    <input type="hidden" name="d_longitude" id="destination_longitude">
                    <input type="hidden" name="current_longitude" id="long">
                    <input type="hidden" name="current_latitude" id="lat">
                    <input type="hidden" name="request_id" id="request_id" value="{{$request->id}}">
                    
                    <div id="map" style="width: 100%; height: 450px;"></div>
                	
				</div>
				<div class="modal-footer">
					<button type="button" class="full-primary-btn locchange">Submit</button>
				</div>
				</form>
			</div>
		</div>
	</div>

</div>
<style type="text/css">
	.cancel_hide{
		display: none;
	}
	.cancel_show{
		display: inline-block;
	}
	.pac-container {
    	z-index: 10000 !important;
	}
</style>
@endsection

@section('scripts')
<script type="text/javascript">
    var current_latitude = 13.0574400;
    var current_longitude = 80.2482605;
    function disableEnterKey(e)
    {
        var key;
        if(window.e)
            key = window.e.keyCode; // IE
        else
            key = e.which; // Firefox

        if(key == 13)
            return e.preventDefault();
    }
    $('#change-location').on('shown.bs.modal', function(){
    	$('#origin-input').trigger('blur').val('{{$request->s_address}}');
    	initMap();
    });

    $('.locchange').on('click',function(){
    	var address=$("#destination-input").val();
    	if(address.length<=0){
    		alert('Drop location required');
    	}
    	else{
    		var latitude=$("destination_latitude").val();
    		var longitude=$("destination_longitude").val();
    		var request_id=$("request_id").val();
    		$.ajax({
				url: "{{ url('/extend/trip') }}",
				type: 'post',
				data: {
					_token : '{{ csrf_token() }}',
					request_id:$("#request_id").val(),
					longitude:$("#destination_longitude").val(),
					latitude:$("#destination_latitude").val(),
					address:address,
				},
				success:function(data) {
					window.location.replace("/dashboard");
				}
			});
    	}
    	
    })
    

</script>
<script type="text/javascript">

    if( navigator.geolocation ) {
       navigator.geolocation.getCurrentPosition( success, fail );
    } else {
        console.log('Sorry, your browser does not support geolocation services');
        initMap();
    }

    function success(position)
    {
        document.getElementById('long').value = position.coords.longitude;
        document.getElementById('lat').value = position.coords.latitude

        if(position.coords.longitude != "" && position.coords.latitude != ""){
            current_longitude = position.coords.longitude;
            current_latitude = position.coords.latitude;
        }
        initMap();
    }

    function fail()
    {
        // Could not obtain location
        console.log('unable to get your location');
        initMap();
    }
</script> 
<script type="text/javascript" src="{{ asset('asset/js/map.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ Config::get('constants.map_key') }}&libraries=places&callback=initMap" async defer></script>

    <script type="text/javascript" src="{{asset('asset/js/rating.js')}}"></script>    
	<script type="text/javascript">
		$('.rating').rating();
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
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.13.3/react.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.13.3/JSXTransformer.js"></script>

    <script type="text/jsx">
    	function interval(){
	        clearInterval(this.updateInterval);
	    }

		var MainComponent = React.createClass({

			getInitialState: function () {
               return {data: [], currency : "{{config('constants.currency')}}"};
            },
                
			componentDidMount: function(){
				$.ajax({
			      url: "{{url('status')}}",
			      type: "GET"})
			      .done(function(response){

				        this.setState({
				            data:response.data[0]
				        });

				    }.bind(this));

			    this.updateInterval = setInterval(this.checkRequest, 5000);

            	interval = interval.bind(this);
			},
			checkRequest : function(){
				$.ajax({
			      url: "{{url('status')}}",
			      type: "GET"})
			      .done(function(response){
				        this.setState({
				            data:response.data[0]
				        });

				    }.bind(this));
			},

			render: function(){

				return (
					<div>
						<SwitchState checkState={this.state.data} currency={this.state.currency}/>
					</div>
				);
			}
		});

		var SwitchState = React.createClass({

			componentDidMount: function() {
				this.changeLabel;
			},

			changeLabel : function(){
				if(this.props.checkState == undefined){
					window.location.reload();
				}else if(this.props.checkState != ""){

					if(this.props.checkState.status == 'SEARCHING'){
						$("#ride_status").text("@lang('user.ride.finding_driver')");
					}else if(this.props.checkState.status == 'STARTED'){
						var provider_name = this.props.checkState.provider.first_name;
						$("#ride_status").text(provider_name+" @lang('user.ride.accepted_ride')");
					}else if(this.props.checkState.status == 'ARRIVED'){
						var provider_name = this.props.checkState.provider.first_name;
						$("#ride_status").text(provider_name+" @lang('user.ride.arrived_ride')");
					}else if(this.props.checkState.status == 'PICKEDUP'){
						console.log(this.props.checkState.status);
						$("#show_location").show();
						$("#ride_status").text("@lang('user.ride.onride')");
					}else if(this.props.checkState.status == 'DROPPED'){
						$("#ride_status").text("@lang('user.ride.waiting_payment')");
						$("#show_location").hide();
					}else if(this.props.checkState.status == 'COMPLETED'){
						var provider_name = this.props.checkState.provider.first_name;
						$("#ride_status").text("@lang('user.ride.rate_and_review') " +provider_name );
					}
					setTimeout(function(){
						$('.rating').rating();
					},400);

				}else{
					$("#ride_status").text('Text will appear here');
				}
			},

			render: function(){

				if(this.props.checkState != ""){

					this.changeLabel();
					if(this.props.checkState.status == 'SEARCHING'){
						return (
							<div>
								<Searching checkState={this.props.checkState} />
							</div>
						);
					}else if(this.props.checkState.status == 'STARTED'){
						return (
							<div>
								<Accepted checkState={this.props.checkState} />
							</div>
						);
					}else if(this.props.checkState.status == 'ARRIVED'){
						return (
							<div>
								<Arrived checkState={this.props.checkState} />
							</div>
						);
					}else if(this.props.checkState.status == 'PICKEDUP'){
						return (
							<div>
								<Pickedup checkState={this.props.checkState} />
							</div>
						);
					}else if((this.props.checkState.status == 'DROPPED' || this.props.checkState.status == 'COMPLETED') && this.props.checkState.payment_mode == 'CASH' && this.props.checkState.paid == 0){
						return (
							<div>
								<DroppedAndCash checkState={this.props.checkState} currency={this.props.currency} />
							</div>
						);
					}else if((this.props.checkState.status == 'DROPPED' || this.props.checkState.status == 'COMPLETED') && this.props.checkState.payment_mode != 'CASH' && this.props.checkState.paid == 0){
						return (
							<div>
								<DroppedAndPayment checkState={this.props.checkState} currency={this.props.currency} />
							</div>
						);
					}else if(this.props.checkState.status == 'COMPLETED'){
						return (
							<div>
								<Review checkState={this.props.checkState} />
							</div>
						);
					}
				}else{
					return ( 
						<p></p>
					 );
				}
			}
		});

		var Searching = React.createClass({
			render: function(){
				return (
					<form action="{{url('cancel/ride')}}" method="POST">
						{{ csrf_field() }}</input>
						<input type="hidden" name="request_id" value={this.props.checkState.id} />
			            <div className="status">
			                <h6>@lang('user.status')</h6>
			                <p>@lang('user.ride.finding_driver')</p>
			            </div>

		            	<button type="submit" className="full-primary-btn fare-btn">@lang('user.ride.cancel_request')</button> 
		            </form>
				);
			}
		});

		var Accepted = React.createClass({
			render: function(){
				return (
					<form action="{{url('cancel/ride')}}" method="POST">
						{{ csrf_field() }}</input>
					<input type="hidden" name="request_id" value={this.props.checkState.id} />
						<div className="status">
			                <h6>@lang('user.status')</h6>
			                <p>@lang('user.ride.accepted_ride')</p>
			            </div>
			            <CancelReason/>
		            	<button type="button" className="full-primary-btn" data-toggle="modal" data-target="#cancel-reason">@lang('user.ride.cancel_request')</button>
		            	<br/>
		            		<h5><strong>@lang('user.ride.ride_details')</strong></h5>
		            	<div className="driver-details">
			            	<dl className="dl-horizontal left-right">
			            		<dt>@lang('user.booking_id')</dt>
				                <dd>{this.props.checkState.booking_id}</dd>
				                <dt>@lang('user.service_type')</dt>
				            	<dd>{{$request->service_type->name}}</dd>
				                <dt>@lang('user.driver_name')</dt>
				                <dd>{this.props.checkState.provider.first_name} {this.props.checkState.provider.last_name}</dd>
				                <dt>@lang('user.service_number')</dt>
				                <dd>{this.props.checkState.provider_service.service_number}</dd>
				                <dt>@lang('user.service_model')</dt>
				                <dd>{this.props.checkState.provider_service.service_model}</dd>
				                <dt>@lang('user.driver_rating')</dt>
				                <dd>
				                	<div className="rating-outer">
			                            <input type="hidden" value={this.props.checkState.provider.rating} name="rating" className="rating" disabled/>
			                        </div>
				                </dd>
				                <dt>@lang('user.payment_mode')</dt>
				                <dd>{this.props.checkState.payment_mode}</dd>
								@if($request->ride_otp == 1)
				                <dt>@lang('user.otp')</dt>
				                <dd>{this.props.checkState.otp}</dd>
								@endif
				            </dl> 
			            </div>

		            </form>
				);
			}
		});

		var CancelReason = React.createClass({
			render: function(){
				return (
					<div id="cancel-reason" className="modal fade" role="dialog">
						<div className="modal-dialog">
							<div className="modal-content">
								<div className="modal-header">
									<button type="button" className="close" data-dismiss="modal">&times;</button>
									<h4 className="modal-title">@lang('user.ride.cancel_request')</h4>
								</div>
								<div className="modal-body">
									<select className="form-control" name="cancel_reason" id="cancel_reason">
										@if($request->reasons)
											@foreach($request->reasons as $reas)
												<option value="{{$reas->reason}}">{{$reas->reason}}</option>
											@endforeach
										@endif	
										<option value="ot">Others</option>
									</select>	
									<textarea className="form-control @if($request->reasons) cancel_hide @endif" id="cancel_text" name="cancel_reason_opt" placeholder="@lang('user.ride.cancel_reason')" row="5"></textarea>
								</div>
								<div className="modal-footer">
									<button type="submit" className="full-primary-btn fare-btn">@lang('user.ride.cancel_request')</button>
								</div>
							</div>
						</div>
					</div>
				);
			}
		});

		var Arrived = React.createClass({
			render: function(){
				return (
					<form action="{{url('cancel/ride')}}" method="POST">
						{{ csrf_field() }}</input>
					<input type="hidden" name="request_id" value={this.props.checkState.id} />
						<div className="status">
			                <h6>@lang('user.status')</h6>
			                <p>@lang('user.ride.arrived_ride')</p>
			            </div>
			            <CancelReason/>
		            	<button type="button" className="full-primary-btn" data-toggle="modal" data-target="#cancel-reason">@lang('user.ride.cancel_request')</button> 
		            	<br/>
		            		<h5><strong>@lang('user.ride.ride_details')</strong></h5>
		            	<div className="driver-details">
			            	<dl className="dl-horizontal left-right">
			            		<dt>@lang('user.booking_id')</dt>
				                <dd>{this.props.checkState.booking_id}</dd>
				                <dt>@lang('user.service_type')</dt>
				            	<dd>{{$request->service_type->name}}</dd>
				                <dt>@lang('user.driver_name')</dt>
				                <dd>{this.props.checkState.provider.first_name} {this.props.checkState.provider.last_name}</dd>
				                <dt>@lang('user.service_number')</dt>
				                <dd>{this.props.checkState.provider_service.service_number}</dd>
				                <dt>@lang('user.service_model')</dt>
				                <dd>{this.props.checkState.provider_service.service_model}</dd>
				                <dt>@lang('user.driver_rating')</dt>
				                <dd>
				                	<div className="rating-outer">
			                            <input type="hidden" value={this.props.checkState.provider.rating} name="rating" className="rating" disabled/>
			                        </div>
				                </dd>
				                <dt>@lang('user.payment_mode')</dt>
				                <dd>{this.props.checkState.payment_mode}</dd>
								@if($request->ride_otp == 1)
				                 <dt>@lang('user.otp')</dt>
				                <dd>{this.props.checkState.otp}</dd>
								@endif
				            </dl> 
			            </div>
		            </form>
				);
			}
		});

		var Pickedup = React.createClass({
			render: function(){
				return (
				<div>
					<div className="status">
		                <h6>@lang('user.status')</h6>
		                <p>@lang('user.ride.onride')</p>
		            </div>
		            <br/>
	            		<h5><strong>@lang('user.ride.ride_details')</strong></h5>
	            	<div className="driver-details">
		            	<dl className="dl-horizontal left-right">
		            		<dt>@lang('user.booking_id')</dt>
				            <dd>{this.props.checkState.booking_id}</dd>
				            <dt>@lang('user.service_type')</dt>
				            <dd>{{$request->service_type->name}}</dd>
			                <dt>@lang('user.driver_name')</dt>
			                <dd>{this.props.checkState.provider.first_name} {this.props.checkState.provider.last_name}</dd>
			                <dt>@lang('user.service_number')</dt>
				                <dd>{this.props.checkState.provider_service.service_number}</dd>
				                <dt>@lang('user.service_model')</dt>
				                <dd>{this.props.checkState.provider_service.service_model}</dd>
			                <dt>@lang('user.driver_rating')</dt>
				                <dd>
				                	<div className="rating-outer">
			                            <input type="hidden" value={this.props.checkState.provider.rating} name="rating" className="rating" disabled/>
			                        </div>
				                </dd>
			                <dt>@lang('user.payment_mode')</dt>
			                <dd>{this.props.checkState.payment_mode}</dd>
			                
			            </dl> 
		            </div>
		        </div>
				);
			}
		});

		var DroppedAndCash = React.createClass({

			render: function(){
				let fixval=2;
				return (
				<div>
					<div className="status">
		                <h6>@lang('user.status')</h6>
		                <p>@lang('user.ride.dropped_ride')</p>
		            </div>
		            <br/>
		            	<h5><strong>@lang('user.ride.ride_details')</strong></h5>
		            	<dl className="dl-horizontal left-right">
		            		<dt>@lang('user.booking_id')</dt>
				            <dd>{this.props.checkState.booking_id}</dd>
				            <dt>@lang('user.service_type')</dt>
				            <dd>{{$request->service_type->name}}</dd>
		            		<dt>@lang('user.driver_name')</dt>
			                <dd>{this.props.checkState.provider.first_name} {this.props.checkState.provider.last_name}</dd>
			                <dt>@lang('user.service_number')</dt>
				                <dd>{this.props.checkState.provider_service.service_number}</dd>
				                <dt>@lang('user.service_model')</dt>
				                <dd>{this.props.checkState.provider_service.service_model}</dd>
			                <dt>@lang('user.driver_rating')</dt>
			                <dd>
			                	<div className="rating-outer">
		                            <input type="hidden" value={this.props.checkState.provider.rating} name="rating" className="rating" disabled/>
		                        </div>
			                </dd>
		            		<dt>@lang('user.payment_mode')</dt>
                        	<dd>{this.props.checkState.payment_mode}</dd>
                        </dl>
		            	<h5><strong>@lang('user.ride.invoice')</strong></h5>
		            	<dl className="dl-horizontal left-right">
                            <dt>@lang('user.ride.distance_travelled')</dt>
                            <dd>{this.props.checkState.distance} {{config('constants.distance')}}</dd>
                            <dt>@lang('user.ride.base_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.fixed.toFixed(fixval)}</dd>
                            @if($request->service_type->calculator == 'MIN')
                            <dt>@lang('user.ride.minutes_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.minute.toFixed(fixval)}</dd>
                            @endif
                            @if($request->service_type->calculator == 'HOUR')
                            <dt>@lang('user.ride.hours_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.hour.toFixed(fixval)}</dd>
                            @endif
                            @if($request->service_type->calculator == 'DISTANCE')
                            <dt>@lang('user.ride.distance_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.distance.toFixed(fixval)}</dd>
                            @endif
                            @if($request->service_type->calculator == 'DISTANCEMIN')
                            <dt>@lang('user.ride.minutes_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.minute.toFixed(fixval)}</dd>
                            <dt>@lang('user.ride.distance_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.distance.toFixed(fixval)}</dd>
                            @endif
                            @if($request->service_type->calculator == 'DISTANCEHOUR')
                            <dt>@lang('user.ride.hours_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.hour.toFixed(fixval)}</dd>
                            <dt>@lang('user.ride.distance_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.distance.toFixed(fixval)}</dd>
                            @endif
                            <dt>@lang('user.ride.toll_charge')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.toll_charge.toFixed(fixval)}</dd>
                            <dt>@lang('user.ride.tax_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.tax.toFixed(fixval)}</dd>

                            {this.props.checkState.payment.round_of ?
								<span>
								<dt>@lang('user.ride.round_off')</dt>
                            	<dd>{this.props.currency}{this.props.checkState.payment.round_of.toFixed(fixval)}</dd>  
                            	</span>
                            : ''
                            }

                            {this.props.checkState.use_wallet ?
								<span>
								<dt>@lang('user.ride.detection_wallet')</dt>
                            	<dd>{this.props.currency}{this.props.checkState.payment.wallet.toFixed(fixval)}</dd>  
                            	</span>
                            : ''
                            }
                            {this.props.checkState.payment.discount ?
								<span>
								<dt>@lang('user.ride.promotion_applied')</dt>
                            	<dd>{this.props.currency}{this.props.checkState.payment.discount.toFixed(fixval)}</dd>  
                            	</span>
                            : ''
                            }
                            {this.props.checkState.payment.tips ?
								<span>
								<dt>@lang('user.ride.tips')</dt>
                            	<dd>{this.props.currency}{this.props.checkState.payment.tips.toFixed(fixval)}</dd>  
                            	</span>
                            : ''
                            }
                            <dt className="big">@lang('user.ride.total')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.total.toFixed(fixval)}</dd> 
                            <dt className="big">@lang('user.ride.amount_paid')</dt>
                            <dd className="big">{this.props.currency}{this.props.checkState.payment.payable.toFixed(fixval)}</dd>
                        </dl>
		        </div>
				);
			}
		});

		var DroppedAndPayment = React.createClass({
			

			getInitialState: function() {
			    return {tips: this.props.checkState.payment.tips,payable: this.props.checkState.payment.payable,total: this.props.checkState.payment.total};
			},

			handletipsChange(event) {

			    this.setState({
			      payable: parseFloat(this.props.checkState.payment.payable)+parseFloat(event.target.value)||this.props.checkState.payment.payable,
			      total: parseFloat(this.props.checkState.payment.total)+parseFloat(event.target.value)||this.props.checkState.payment.total
			  });
			},
			
			render: function(){
				let fixval=2;				
				return (
				<div>
					<form method="POST" action="{{url('/payment')}}">
						{{ csrf_field() }}</input>
					<div className="status">
		                <h6>@lang('user.status')</h6>
		                <p>@lang('user.ride.dropped_ride')</p>
		            </div>
		            	<br/>
		            	<h5><strong>@lang('user.ride.ride_details')</strong></h5>
		            	<dl className="dl-horizontal left-right">
		            		<dt>@lang('user.booking_id')</dt>
				            <dd>{this.props.checkState.booking_id}</dd>
				            <dt>@lang('user.service_type')</dt>
				            <dd>{{$request->service_type->name}}</dd>
		            		<dt>@lang('user.driver_name')</dt>
			                <dd>{this.props.checkState.provider.first_name} {this.props.checkState.provider.last_name}</dd>
			                <dt>@lang('user.service_number')</dt>
				                <dd>{this.props.checkState.provider_service.service_number}</dd>
				                <dt>@lang('user.service_model')</dt>
				                <dd>{this.props.checkState.provider_service.service_model}</dd>
			                <dt>@lang('user.driver_rating')</dt>
			                <dd>
			                	<div className="rating-outer">
		                            <input type="hidden" value={this.props.checkState.provider.rating} name="rating" className="rating" disabled/>
		                        </div>
			                </dd>
		            		<dt>@lang('user.payment_mode')</dt>
                        	<dd>{this.props.checkState.payment_mode}</dd>
                        </dl>
		            	<h5><strong>@lang('user.ride.invoice')</strong></h5>
		            	<input type="hidden" name="request_id" value={this.props.checkState.id} />
		            	<dl className="dl-horizontal left-right">
		            		<dt>@lang('user.ride.distance_travelled')</dt>
                            <dd>{this.props.checkState.distance} {{config('constants.distance')}}</dd>
                            <dt>@lang('user.ride.base_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.fixed.toFixed(fixval)}</dd>
                            @if($request->service_type->calculator == 'MIN')
                            <dt>@lang('user.ride.minutes_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.minute.toFixed(fixval)}</dd>
                            @endif
                            @if($request->service_type->calculator == 'HOUR')
                            <dt>@lang('user.ride.hours_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.hour.toFixed(fixval)}</dd>
                            @endif
                            @if($request->service_type->calculator == 'DISTANCE')
                            <dt>@lang('user.ride.distance_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.distance.toFixed(fixval)}</dd>
                            @endif
                            @if($request->service_type->calculator == 'DISTANCEMIN')
                            <dt>@lang('user.ride.minutes_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.minute.toFixed(fixval)}</dd>
                            <dt>@lang('user.ride.distance_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.distance.toFixed(fixval)}</dd>
                            @endif
                            @if($request->service_type->calculator == 'DISTANCEHOUR')
                            <dt>@lang('user.ride.hours_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.hour.toFixed(fixval)}</dd>
                            <dt>@lang('user.ride.distance_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.distance.toFixed(fixval)}</dd>
                            @endif
                            <dt>@lang('user.ride.tax_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.tax.toFixed(fixval)}</dd>
                            <dt>@lang('user.ride.toll_charge')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.toll_charge.toFixed(fixval)}</dd>
                            
                            {this.props.checkState.payment.round_of ?
								<span>
								<dt>@lang('user.ride.round_off')</dt>
                            	<dd>{this.props.currency}{this.props.checkState.payment.round_of.toFixed(fixval)}</dd>  
                            	</span>
                            : ''
                            }

                            {this.props.checkState.use_wallet ?
								<span>
								<dt>@lang('user.ride.detection_wallet')</dt>
                            	<dd>{this.props.currency}{this.props.checkState.payment.wallet.toFixed(fixval)}</dd>  
                            	</span>
                            : ''
                            }
                            {this.props.checkState.payment.discount ?
								<span>
								<dt>@lang('user.ride.promotion_applied')</dt>
                            	<dd>{this.props.currency}{this.props.checkState.payment.discount.toFixed(fixval)}</dd>  
                            	</span>
                            : ''
                            }

                            <dt>@lang('user.ride.total')</dt>
                            <dd>{this.props.currency}{this.state.total.toFixed(fixval)}</dd> 
                            <dt className="big">@lang('user.ride.amount_paid')</dt>
                            <dd className="big">{this.props.currency}{this.state.payable.toFixed(fixval)}</dd>
                            <dt>@lang('user.ride.tips')</dt>
                            <dd>{this.props.currency}<input type="number" min="0" name="tips" id="tips" onChange={this.handletipsChange}/></dd>
                        </dl>
                    	<button type="submit" className="full-primary-btn fare-btn">CONTINUE TO PAY - {this.props.currency}{this.state.payable.toFixed(fixval)}</button>   
                    </form>
		        </div>
				);
			}
		});

		var Review = React.createClass({
			render: function(){
				interval();
				return (
				<form method="POST" action="{{url('/rate')}}">
				{{ csrf_field() }}</input>
                    <div className="rate-review">
                        <label>@lang('user.ride.rating')</label>
                        <div className="rating-outer">
                            <input type="hidden" value="1" name="rating" className="rating"/>
                        </div>
						<input type="hidden" name="request_id" value={this.props.checkState.id} />
                        <label>@lang('user.ride.comment')</label>
                        <textarea className="form-control" name="comment" placeholder="Write Comment"></textarea>
                    </div>
                    <button type="submit" className="full-primary-btn fare-btn">SUBMIT</button>   
                </form>
				);
			}
		});

		React.render(<MainComponent/>,document.getElementById("container"));
	</script>
<style type="text/css">
	#tips{
		width:50px;text-align:right;
	}
</style>
@endsection
