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

@extends('provider.layout.app')

@section('content')
<div class="pro-dashboard-head">
        <div class="container">
            <a href="{{url('provider/earnings')}}" class="pro-head-link active">@lang('provider.partner.payment')</a>
             <!-- <a href="{{url('provider/upcoming')}}" class="pro-head-link">@lang('provider.partner.upcoming')</a> -->
   <!--         <a href="new-provider-patner-invoices.html" class="pro-head-link">Payment Invoices</a>
            <a href="new-provider-banking.html" class="pro-head-link">Banking</a> -->
        </div>
    </div>

    <div class="pro-dashboard-content">
        <!-- Earning head -->
        <div class="earning-head">
            <div class="container">
                <div class="earning-element">
                    <p class="earning-txt">@lang('provider.partner.total_earnings')</p>
                    <p class="earning-price" id="set_fully_sum">00.00</p>
                </div>
                <div class="earning-element row no-margin">

                 <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
                        <div class="earning-box">
                            <p class="dashboard-count">{{$today}}</p>
                            <p class="dashboard-txt">@lang('provider.partner.trips_completed')</p>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
                        <div class="earning-box">
                            <p class="dashboard-count">{{config('constants.daily_target',0)}}</p>
                            <p class="dashboard-txt">@lang('provider.partner.daily_trip') </p>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
                        <div class="earning-box">
                            <p class="dashboard-count">{{$provider[0]->accepted->count()}}</p>
                            <p class="dashboard-txt">@lang('provider.partner.fully_completed')</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
                        <div class="earning-box">
                            <p class="dashboard-count">
                            @if($provider[0]->accepted->count() != 0)
                                {{$provider[0]->accepted->count()/$provider[0]->accepted->count()*100}}%
                            @else
                            	0%
                            @endif
                            </p>
                            <p class="dashboard-txt">@lang('provider.partner.acceptance')</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
                        <div class="earning-box">
                            <p class="dashboard-count">
                                {{$provider[0]->cancelled->count()}}
                            </p>
                            <p class="dashboard-txt">@lang('provider.partner.driver_cancel')</p>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <!-- End of earning head -->

        <!-- Earning Content -->
        <div class="earning-content gray-bg">
            <div class="container">

                <!-- Earning section -->
                <div class="earning-section pad20 row no-margin">
                    <div class="earning-section-head">
                        <h3 class="earning-section-tit">@lang('provider.partner.weekly_earning')</h3>
                    </div>

                    <!-- Earning acc-wrapper -->
                    <div class="col-lg-7 col-md-8 col-sm-10 col-xs-12 no-padding"">
                        <div class="earn-acc-wrapper">
                            <div class="earning-acc pad20">
                                <!-- Earning acc head -->
                                <div class="row no-margin">
                                    <div class="pull-left trip-left">
                                        <h3 data-toggle="collapse" data-target="#demo1" class="accordion-toggle collapsed acc-tit">
                                            <span class="arrow-icon fa fa-chevron-right"></span>@lang('provider.partner.trip_earning')
                                        </h3>
                                    </div>
                                </div>
                                <!-- End of eaning acc head -->
                                <!-- Earning acc body -->
                                <div class="accordian-body earning-acc-body collapse row" id="demo1">
                                    <table class="table table-condensed table-responsive" style="border-collapse:collapse;">
                                        <tbody>
                                        <?php $sum_weekly = 0; ?>
                                        @foreach($weekly as $day)
                                            <tr>
                                                <td>
                                                @if($day->created_at)
                                                    {{date('Y-m-d',strtotime($day->created_at))}} - {{$day->created_at->diffForHumans()}}
                                                @else
                                                    -
                                                @endif
                                                </td>
                                                <td class="text-right">
                                                @if($day->payment != "")
                                                {{currency($day->payment->provider_pay)}}
                                                @else
                                                {{currency(0.00)}}
                                                @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- End of earning acc-body -->
                            </div>
                            <div class="earning-acc pad20 border-top">
                                <div class="row no-margin">
                                    <div class="pull-left trip-left">
                                        <h3 class="acc-tit estimate-tit">
                                            @lang('provider.partner.estimate_payout')
                                        </h3>
                                    </div>

                                    <div class="pull-right trip-right">
                                        <p class="earning-cost no-margin">{{currency($weekly_sum)}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of earning acc-wrapper -->
                </div>
                <!-- End of earning section -->

                <!-- Earning section -->
                <div class="earning-section earn-main-sec pad20">
                    <!-- Earning section head -->
                    <div class="earning-section-head row no-margin">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-left-padding">
                            <h3 class="earning-section-tit">@lang('provider.partner.daily_earnings')</h3>
                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                            <div class="daily-earn-right text-right">
                                <div class="status-block display-inline row no-margin">
                                    <!-- <form class="form-inline status-form">
                                        <div class="form-group">
                                            <label>@lang('provider.partner.status')</label>
                                            <select type="password" class="form-control mx-sm-3">
                                                <option>@lang('provider.partner.all_trip')</option>
                                                <option>@lang('provider.partner.completed')</option>
                                                <option>@lang('provider.partner.pending')</option>
                                            </select>
                                        </div>
                                    </form> -->
                                </div>
                                <!-- View tab -->

                                <!-- End of view tab -->
                            </div>
                        </div>
                    </div>
                    <!-- End of earning section head -->

                    <!-- Earning-section content -->
                    <div class="tab-content list-content">
                        <div class="list-view pad30 ">

                            <table class="earning-table table table-responsive">
                                <thead>
                                    <tr>
                                        <th>@lang('provider.partner.pickup')</th>
                                        <th>@lang('provider.partner.booking_id')</th>
                                        <th>@lang('provider.partner.vehicle')</th>
                                        <th>@lang('provider.partner.duration')</th>
                                        <th>@lang('provider.partner.status')</th>
                                        <th>@lang('provider.partner.distance(km)')</th>
                                        <!-- <th>@lang('provider.partner.invoice_amount')</th>
                                        <th>@lang('provider.partner.cash_collected')</th> -->
                                        <th>@lang('provider.partner.total_earnings')</th>
                                        <th>@lang('provider.partner.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $fully_sum = 0; ?>
                                @foreach($fully as $each)
                                    <tr>
                                        <td>{{date('Y D, M d - H:i A',strtotime($each->created_at))}}</td>
                                        <td>{{ $each->booking_id }}</td>
                                        <td>
                                        	@if($each->service_type)
                                        		{{$each->service_type->name}}
                                        	@endif
                                        </td>
                                        <td>
                                        	@if($each->finished_at != null && $each->started_at != null) 
                                                <?php 
                                                $StartTime = \Carbon\Carbon::parse($each->started_at);
                                                $EndTime = \Carbon\Carbon::parse($each->finished_at);
                                                echo $StartTime->diffInHours($EndTime)." "; ?>@lang('provider.hours')
                                                <?php
                                                echo " ".$StartTime->diffInMinutes($EndTime)." ";
                                                ?>
                                                 @lang('provider.minutes')
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{$each->status}}<br /> {{ $each->cancel_reason }}</td>
                                        <td>{{$each->distance}}{{$each->unit}}</td>
                                        <!-- <td> 
                                            @if($each->payment != "")
                                            {{currency($each->payment->total)}}
                                            @else
                                            {{currency(0.00)}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($each->payment != "")
                                                <?php $each_sum = 0;
                                                $each_sum = $each->payment->provider_pay;
                                                $fully_sum += $each_sum;
                                                ?>
                                                {{currency($each_sum)}}
                                            @else
                                            -    
                                            @endif
                                        </td> -->
                                        <td>@if($each->status=='CANCELLED')- @else{{currency($fully_sum)}}@endif</td>
                                        <td><button id="{{$each->id}}" class="Dispute" value="{{$each->id}}">Dispute</button></td>

                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>
                <!-- End of earning section -->
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
<!-- Modal content end-->

            </div>
        </div>
        <!-- Endd of earning content -->
    </div>                
</div>
@endsection

@section('scripts')
<script type="text/javascript">
	document.getElementById('set_fully_sum').textContent = "{{currency($fully_sum)}}";
    // Get the modal
var modal = document.getElementById('myModal');

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];
// When the user clicks the button, open the modal 

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
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
  // modal1.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
var trip_id;
      /* Click Dispute */
      $(document).on("click", ".Dispute", function() {
        trip_id = this.id;
        modal.style.display = "block";
            var url = "{{url('provider/dispute','tripId')}}";
            var disputeMsgBody;
            url = url.replace('tripId', trip_id);
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
                        if(item.dispute_type =="provider"){
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
                            <input type="submit" name="submitbtn" class="submitbtn" id="sendBtn" value="send">`);
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
                          <input type="submit" name="submitbtn" class="submitbtn" id="disputeSendBtn" value="send">`);
                 }
                }
            });
    });
      /* Save dispute Record */
      
      $(document).on("click", "#disputeSendBtn", function(event) {
        event.preventDefault();
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
        var url = "{{url('provider/dispute')}}";
        $.ajax({            
            url: url,
            type: 'POST',
            data: {
                _token:'{{csrf_token()}}',
                dispute_name: $("#disputeReason").val(), 
                dispute_title: $("#disputeTitle").val(), 
                comments: $("#disputeMsg").val(),
                request_id: trip_id,
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
                        modal.style.display = "none";
                // $(".Dispute").trigger("click");
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