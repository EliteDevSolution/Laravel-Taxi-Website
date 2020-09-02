<style>
/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}
.modal1 {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content,.modal-content1 {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}


/* The Close Button */
.close,.close1 {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,.close1:hover,
.close:focus,.close1:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
</style>
@extends('user.layout.base')

@section('title', 'My Trips ')

@section('content')

<div class="col-md-9">
    

    <div class="dash-content">
        <div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content text-center">
      <span class="close">&times;</span>
      <div id="disputeHead">
                <div class="row dis">
                  <div id="disputeBody">
                  <div id="disputetitle">
                            </div>
                      <div id="disputeReasonBody">
                            </div>
                          
                            <div class="col-xs-3" id="disputename">
                            </div>
                            <div class="col-xs-7" id="disputemsg">
                            </div>
                        </div>
                  </div>
                <div id="disputeMsgBody">
               </div>    
            </div>
            </div>
  </div>


<div id="myModal1" class="modal1">

  <!-- Modal content -->
        <div class="modal-content1 text-center">
         <span class="close1">&times;</span>
         <div id="iteamHead">
                <div class="row dis">
                  <div id="lostitemBody">
                           <div id="title">
                            </div>
                            <div class="col-xs-3" id="name">
                            </div>
                            <div class="col-xs-7" id="msg">
                            </div>
                        </div>
           </div>
            <div id="lostitemMsgBody">
           </div>    
            </div>
            </div>
    </div>
        

        <div class="row no-margin">
            <div class="col-md-12">
                <h4 class="page-title">@lang('user.my_trips')</h4>
            </div>
        </div>

        <div class="row no-margin ride-detail">
            <div class="col-md-12">
            @if($trips->count() > 0)

                <table class="table table-condensed" style="border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>@lang('user.booking_id')</th>
                            <th>@lang('user.date')</th>
                            <th>@lang('user.profile.name')</th>
                            <th>@lang('user.amount')</th>
                            <th>@lang('user.type')</th>
                            <th>@lang('user.payment')</th>
                            
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($trips as $trip)

                        <tr data-toggle="collapse" data-target="#trip_{{$trip->id}}" class="accordion-toggle collapsed">
                            <td><span class="arrow-icon fa fa-chevron-right"></span></td>
                            <td>{{ $trip->booking_id }}</td>
                            <td>{{date('d-m-Y',strtotime($trip->assigned_at))}}</td>
                            @if($trip->provider)
                                <td>{{$trip->provider->first_name}} {{$trip->provider->last_name}}</td>
                            @else
                                <td>-</td>
                            @endif
                            @if($trip->payment)
                                <td>{{currency($trip->payment->total)}}</td>
                            @else
                                <td>-</td>
                            @endif
                            @if($trip->service_type)
                                <td>{{$trip->service_type->name}}</td>
                            @else
                                <td>-</td>
                            @endif
                            <td>@lang('user.paid_via') {{$trip->payment_mode}}</td>
                           <!--  <td><input type="button" class="dispute" value="Dispute"/></td> -->
                        </tr>

                        <tr class="hiddenRow">
                            <td colspan="12">
                                <div class="accordian-body collapse row" id="trip_{{$trip->id}}">
                                    <div class="col-md-6">
                                        <div class="my-trip-left">
                                        <?php 
                                    $map_icon = asset('asset/img/marker-start.png');
                                    $static_map = "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=600x450&maptype=terrain&format=png&visual_refresh=true&markers=icon:".$map_icon."%7C".$trip->s_latitude.",".$trip->s_longitude."&markers=icon:".$map_icon."%7C".$trip->d_latitude.",".$trip->d_longitude."&path=color:0x191919|weight:8|enc:".$trip->route_key."&key=".Config::get('constants.map_key'); ?>
                                            <div class="map-static">
                                                <img src="{{$static_map}}" height="280px;">
                                            </div>
                                            <div class="from-to row no-margin">
                                                <div class="from">
                                                    <h5>@lang('user.from')</h5>
                                                    <h6>{{date('H:i A - d-m-y', strtotime($trip->started_at))}}</h6>
                                                    <p>{{$trip->s_address}}</p>
                                                </div>
                                                <div class="to">
                                                    <h5>@lang('user.to')</h5>
                                                    <h6>{{date('H:i A - d-m-y', strtotime($trip->finished_at))}}</h6>
                                                    <p>{{$trip->d_address}}</p>
                                                    <input type="hidden" id="trip_id" class="dispute" value="{{$trip->id}}"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                        <div class="mytrip-right">
                                            <div class="whlbtn">
                                <button id="{{$trip->id}}" class="dispute" value="{{$trip->id}}">Dispute</button>
                                <button id="{{$trip->id}}" class="lostitem" value="{{$trip->id}}">LostItem</button>
                                </div>
                                            <div class="fare-break">

                                                <h4 class="text-center">
                                                <strong>
                                                @if($trip->service_type)
                                                    {{$trip->service_type->name}}
                                                @endif
                                                 - @lang('user.fare_breakdown')</strong></h4>

                                                <h5>@lang('user.ride.base_price') <span>
                                                @if($trip->payment)
                                                    {{currency($trip->payment->fixed)}}
                                                @endif
                                                </span></h5>
                                                @if($trip->service_type->calculator=='MIN')
                                                    <h5>@lang('user.ride.minutes_price') <span>
                                                    @if($trip->payment)
                                                        {{currency($trip->payment->minute)}}
                                                    @endif
                                                    </span></h5>
                                                @endif
                                                @if($trip->service_type->calculator=='HOUR')
                                                    <h5>@lang('user.ride.hours_price') <span>
                                                    @if($trip->payment)
                                                        {{currency($trip->payment->hour)}}
                                                    @endif
                                                    </span></h5>
                                                @endif
                                                @if($trip->service_type->calculator=='DISTANCE')
                                                    <h5>@lang('user.ride.distance_price') <span>
                                                    @if($trip->payment)
                                                        {{currency($trip->payment->distance)}}
                                                    @endif
                                                    </span></h5>
                                                @endif
                                                @if($trip->service_type->calculator=='DISTANCEMIN')
                                                    <h5>@lang('user.ride.minutes_price') <span>
                                                    @if($trip->payment)
                                                        {{currency($trip->payment->minute)}}
                                                    @endif
                                                    </span></h5>
                                                    <h5>@lang('user.ride.distance_price') <span>
                                                    @if($trip->payment)
                                                        {{currency($trip->payment->distance)}}
                                                    @endif
                                                    </span></h5>
                                                @endif
                                                @if($trip->service_type->calculator=='DISTANCEHOUR')
                                                    <h5>@lang('user.ride.hours_price') <span>
                                                    @if($trip->payment)
                                                        {{currency($trip->payment->hour)}}
                                                    @endif
                                                    </span></h5>
                                                    <h5>@lang('user.ride.distance_price') <span>
                                                    @if($trip->payment)
                                                        {{currency($trip->payment->distance)}}
                                                    @endif
                                                    </span></h5>
                                                @endif
                                                
                                                 @if($trip->payment)
                                                    @if($trip->payment->wallet)
                                                        <h5>@lang('user.ride.wallet_deduction') <span>
                                                            {{currency($trip->payment->wallet)}}
                                                        </span></h5>
                                                    @endif
                                                @endif
                                                @if($trip->payment)
                                                    @if($trip->payment->discount)
                                                        <h5>@lang('user.ride.promotion_applied') <span>
                                                            {{currency($trip->payment->discount)}}
                                                        </span></h5>
                                                    @endif
                                                @endif
                                                @if($trip->payment)
                                                    @if($trip->payment->tips)
                                                        <h5>@lang('user.ride.tips') <span>
                                                            {{currency($trip->payment->tips)}}
                                                        </span></h5>
                                                    @endif
                                                @endif
                                                <h5><strong>@lang('user.ride.tax_price') </strong><span><strong>
                                                @if($trip->payment)
                                                {{currency($trip->payment->tax)}}
                                                @endif
                                                </strong></span></h5>

                                                @if($trip->payment->waiting_amount>0)
                                                    <h5><strong>@lang('user.ride.waiting_price') </strong><span><strong>
                                                    {{currency($trip->payment->waiting_amount)}}
                                                    </strong></span></h5>
                                                @endif

                                                 @if($trip->payment->round_of)
                                                    <h5><strong>@lang('user.ride.round_off') </strong><span><strong>
                                                    {{currency($trip->payment->round_of)}}
                                                    </strong></span></h5>
                                                @endif

                                                <h5 class="big"><strong>@lang('user.charged') - {{$trip->payment_mode}} </strong><span><strong>
                                                @if($trip->payment)
                                                  @if($trip->payment_mode=='CASH')
                                                    {{currency(round($trip->payment->total-$trip->payment->discount+$trip->payment->tips))}}
                                                  @else
                                                    {{currency($trip->payment->total-$trip->payment->discount+$trip->payment->tips)}}
                                                  @endif  
                                                @endif
                                                </strong></span></h5>

                                            </div>

                                            <div class="trip-user">
                                                <div class="user-img" style="background-image: url({{img($trip->provider->avatar)}});">
                                                </div>
                                                <div class="user-right">
                                                    @if($trip->provider)
                                                        <h5>{{$trip->provider->first_name}} {{$trip->provider->last_name}}</h5>
                                                    @else
                                                    <h5>- </h5>
                                                    @endif
                                                    @if($trip->rating)
                                                    <div class="rating-outer">
                                                        <input type="hidden" class="rating" value="{{$trip->rating->provider_rating}}" disabled="disabled" />

                                                    </div>
                                                    <p>{{$trip->rating->user_comment}}</p>
                                                     @else
                                                        -
                                                    @endif
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                </div>
                            </td>
                        </tr>

                        @endforeach


                    </tbody>
                </table>

                
                @else
                    <hr>
                    <p style="text-align: center;">@lang('user.no_trips')</p>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('asset/js/rating.js') }}"></script>
<script type="text/javascript">
    $('.rating').rating();
</script>
<script>
// Get the modal
var modal = document.getElementById('myModal');
var modal1 = document.getElementById('myModal1');
// Get the button that opens the modal
// var btn = document.getElementById("myBtn");
// var btn1 = document.getElementById("myBtn1");
// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];
var span1 = document.getElementsByClassName("close1")[0];
// When the user clicks the button, open the modal 
// btn.onclick = function() {
//   modal.style.display = "block";
 
// }
// btn1.onclick = function() {
//    modal1.style.display = "block";
// }

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    $("#disputeHead").empty()
                .append(`<div class="row dis">
                  <div id="disputeBody">
                  <div id="disputetitle">
                            </div>
                      <div id="disputeReasonBody">
                            </div>
                          
                            <div class="col-xs-3" id="disputename">
                            </div>
                            <div class="col-xs-7" id="disputemsg">
                            </div>
                                    </div>
                            </div>
                            <div id="disputeMsgBody">
                        </div>    
                        </div>`);
  modal.style.display = "none";
  // modal1.style.display = "none";
}
span1.onclick = function() {

  modal1.style.display = "none";


}
// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
    modal1.style.display = "none";
  }
}
    /* Click LostItem */
    $(document).on("click", ".lostitem", function() {
            var trip_id = this.id;
        
            modal1.style.display = "block";
                var url = "{{url('lostitem','id')}}";
                url = url.replace('id', trip_id);
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(data) {
                        if (data.lostitem.length !=0 && typeof(data.lostitem) !== "undefined" && data.lostitem != null) {
                            $("#lostitemBody").find("#title").empty().append(`<h2 class="loshead">`+data.lostitem[0]['lost_item_name']+`</h2>
                            <input type="hidden"  id="lostitemTitle" name="title" value="`+data.lostitem[0]['lost_item_name']+`"/>`);
                            $("#lostitemBody").find("#name").empty();
                            $("#lostitemBody").find("#msg").empty();
                        $.each(data.lostitem, function(key, item) {
                            $("#lostitemBody").find("#name").append(`<p class="loshead">`+item.comments_by+`</p>`);
                            $("#lostitemBody").find("#msg").append(`<p class="loshead">`+item.comments+`</p>`);
                        });
                        if(data.sendBtn == "yes") 
                        {
                            $("#lostitemMsgBody").empty()
                            .append(` <div class="row dis">
                                    <div class="col-xs-3">
                                    <h2>Status : Closed</h2>
                                    </div>
                                    </div>
                                `);
                        }
                        else{
                        $("#lostitemMsgBody").empty()
                            .append(`<div class="row dis">
                            <div class="col-xs-3">
                                    <h2>Comments</h2>
                                </div>
                                <div class="col-xs-7">    
                                <textarea class="dispdesc" name="msg" id="lostitemMsg" placeholder="Type your message"></textarea>
                            </div>
                            </div>
                            <input type="submit" name="submitbtn" class="submitbtn lostitemBtn" id="`+trip_id+`" value="send">`);
                        }
                        }
                        else{

                            $("#lostitemBody").empty()
                            .append(` <div class="row dis">
                                <h2 class="loshead">Lostitem</h2>
                                <div class="col-xs-3">
                                    <h2>Title</h2>
                                </div>
                                <div class="col-xs-7">
                                <input type="text" class="disptitle" id="lostitemTitle" name="title" placeholder="Enter title"/>
                                </div>
                            </div>`);
                            $("#lostitemMsgBody").empty()
                            .append(`<div class="row dis">
                            <div class="col-xs-3">
                                    <h2>Comments</h2>
                                </div>
                                <div class="col-xs-7">    
                                <textarea class="dispdesc" name="msg" id="lostitemMsg" placeholder="Type your message"></textarea>
                            </div>
                            </div>
                            <input type="submit" name="submitbtn" class="submitbtn lostitemBtn" id="`+trip_id+`" value="send">`);
                    }
                    }
                });
        });
      /* Save lostItem Record */
      $(document).on("click", ".lostitemBtn", function(event) {
                    event.preventDefault();
                    var tripId = this.id
                    if($("#lostitemTitle").val() ==null || $("#lostitemTitle").val() ==''){
                        alert("Please Enter Title");
                    }
                    else if($("#lostitemMsg").val() ==null || $("#lostitemMsg").val() ==''){
                        alert("Please Enter Message");
                    }
                    else{
                    var url = "{{url('lostitem')}}";
                    $.ajax({            
                        url: url,
                        type: 'POST',
                        data: {
                            _token:'{{csrf_token()}}',
                            lost_item_name: $("#lostitemTitle").val(), 
                            comments: $("#lostitemMsg").val(),
                            request_id: tripId,
                        },    
                        success: function(response) {
                            $("#lostitemMsg").val('');
                            $("#iteamHead").empty()
                                .append(`
                                <div class="row dis">
                                <div id="lostitemBody">
                                        <div id="title">
                                            </div>
                                            <div class="col-xs-3" id="name">
                                            </div>
                                            <div class="col-xs-7" id="msg">
                                            </div>
                                        </div>
                        </div>
                            <div id="lostitemMsgBody">
                        </div>`);
                            modal1.style.display = "none";
                            // $("#myBtn1").trigger("click");
                        },
                        error: function(responce) {
                            alert(responce);
                        }
                    });
                    }

               });

        /* Click Dispute */
        $(document).on("click", ".dispute", function() {
            modal.style.display = "block";
            var trip_id = this.id;
            var url = "{{url('dispute','id')}}";
            var disputeMsgBody;
            url = url.replace('id', trip_id);
            $.ajax({
                url: url,
                type: "GET",
                success: function(data) {
                    if(data.dispute.length !=0 && typeof(data.dispute) !== "undefined" && data.dispute != null) {
                        $("#disputeBody").find("#disputeReasonBody").empty().append(`<div class="col-xs-3">
                            <p class="loshead">Reason</p>
                            </div>
                            <div class="col-xs-7">
                            <h2 class="loshead">`+data.dispute[0]['dispute_name']+`</h2></div>`);
                        $("#disputeBody").find("#disputetitle").empty().append(`<h2 class="loshead">`+data.dispute[0]['dispute_title']+`</h2>
                        <input type="hidden"  id="disputeTitle" name="title" value="`+data.dispute[0]['dispute_title']+`"/>`);
                        $("#disputeBody").find("#disputename").empty();
                        $("#disputeBody").find("#disputemsg").empty();
                    $.each(data.dispute, function(key, item) {
                        if(item.dispute_type =="user"){
                            disputeMsgBody="false";
                        }
                        if(typeof(item.comments) !== "undefined" && item.comments != null) {
                        $("#disputeBody").find("#disputename").append(`<p class="loshead">`+item.dispute_type+`</p>`);
                        $("#disputeBody").find("#disputemsg").append(`<p class="loshead">`+item.comments+`</p>`);
                        }
                    });
                    if(data.sendBtn == "yes") 
                    {
                        $("#disputeMsgBody").empty()
                        .append(` <div class="row dis">
                                   <div class="col-xs-3">
                                <h2>Status : Closed</h2>
                                </div>
                                </div>
                            `);
                    }
                    else{
                        if(disputeMsgBody !="false"){
                        $("#disputeMsgBody").empty()
                          .append(`<div class="row dis">
                              <div class="col-xs-3">
                                <h2>Comments</h2>
                             </div>
                             <div class="col-xs-7">    
                             <textarea class="dispdesc" name="msg" id="disputeMsg" placeholder="Type your message"></textarea>
                             </div>
                             </div>
                            <input type="submit" name="submitbtn" class="submitbtn disputeSendBtn" id="`+trip_id+`" value="send">`);
                        }else{
                            $("#disputeMsgBody").empty();
                        }
                    }
                    }
                    else{
                        $("#disputeBody").empty()
                        .append(` <div class="row dis">
                            <h2 class="loshead">dispute</h2>
                            <div class="col-xs-3">
                                <h2>Reasons</h2>
                            </div>
                            <div class="col-xs-7">
                            <select class="form-control" id="disputeReason">
                            </select> 
                             </div>
                             </div>
                             <div class="row dis">
                            <div class="col-xs-3">
                                <h2>Title</h2>
                            </div>
                            <div class="col-xs-7">
                            <input type="text" class="disptitle" id="disputeTitle" name="title" placeholder="Enter title"/>
                            </div>
                        </div>`);
                        $("#disputeReason").empty();
                            $.each(data.disputeReason, function(key, item) {
                                $("#disputeReason").append('<option value="' + item.dispute_name + '">' + item.dispute_name+ '</option>');
                            });
                            $("#disputeReason").append('<option value="others">Others</option>');
                        $("#disputeMsgBody").empty()
                        .append(`<div id="disputeComments">
                           </div>
                          <input type="submit" name="submitbtn" class="submitbtn disputeSendBtn" id="`+trip_id+`" value="send">`);
                 }
                }
            });
    });
      /* Save dispute Record */
      
      $(document).on("click", ".disputeSendBtn", function(event) {
        event.preventDefault();
        var tripId = this.id;
        if($("#disputeReason").val() ==null || $("#disputeReason").val() ==''){
          alert("Please Select Reason");
        }
        if($("#disputeTitle").val() ==null || $("#disputeTitle").val() ==''){
          alert("Please Enter Title");
        }
        else if(($("#disputeReason").val() =='others' ) && ($("#disputeMsg").val() ==null || $("#disputeMsg").val() =='')){
          alert("Please Enter Message");
        }
       else{
        var url = "{{url('dispute')}}";
    
        $.ajax({            
            url: url,
            type: 'POST',
            data: {
                _token:'{{csrf_token()}}',
                dispute_name: $("#disputeReason").val(), 
                dispute_title: $("#disputeTitle").val(), 
                comments: $("#disputeMsg").val(),
                request_id: tripId,
            },    
            success: function(response) {
                $("#disputeHead").empty()
                .append(`<div class="row dis">
                  <div id="disputeBody">
                  <div id="disputetitle">
                            </div>
                      <div id="disputeReasonBody">
                            </div>
                          
                            <div class="col-xs-3" id="disputename">
                            </div>
                            <div class="col-xs-7" id="disputemsg">
                            </div>
                                    </div>
                            </div>
                            <div id="disputeMsgBody">
                        </div>    
                        </div>`);
                // $("#myBtn").trigger("click");
                modal.style.display = "none";

            },
            error: function(responce) {
               alert(responce);
            }
        });
       }

    });

    /* Select Project */
    $(document).on("change", "#disputeReason", function() {
        var disputeReason = $('#disputeReason').val();
        if (disputeReason == 'others') {
            $("#disputeComments").empty()
                .append(`<div class="row dis">
                        <div class="col-xs-3">
                                <h2>Comments</h2>
                            </div>
                            <div class="col-xs-7">    
                            <textarea class="dispdesc" name="msg" id="disputeMsg" placeholder="Type your message"></textarea>
                           </div>
                           </div>`);

        } else {
            $("#disputeComments").empty();
        }
    });
</script>
@endsection
