<?php
if(config('constants.card', 0) == 0 && config('constants.paystack', 0) == 0){
    header('Location:/provider/profile');
    exit;
}   
?>
@extends('provider.layout.app')

@section('content')
<div class="pro-dashboard-head">
    <div class="container">
        <a href="{{ route('provider.profile.index') }}" class="pro-head-link">@lang('provider.profile.profile')</a>
        <a href="{{ route('provider.documents.index') }}" class="pro-head-link">@lang('provider.profile.manage_documents')</a>
        <a href="{{ route('provider.location.index') }}" class="pro-head-link">@lang('provider.profile.update_location')</a>
        <a href="{{route('provider.wallet.transation')}}" class="pro-head-link">@lang('provider.profile.wallet_transaction')</a>
        <a href="#" class="pro-head-link active">@lang('provider.card.list')</a>
        <a href="{{ route('provider.transfer') }}" class="pro-head-link">@lang('provider.profile.transfer')</a>
        @if(config('constants.referral')==1)
            <a href="{{ route('provider.referral') }}" class="pro-head-link">@lang('provider.profile.refer_friend')</a>
        @endif
    </div>
</div>

<div class="pro-dashboard-content gray-bg">
    <div class="container">
        <div class="manage-docs pad30">
            <div class="manage-doc-content">
                <div class="manage-doc-section pad50">
                    <div class="manage-doc-section-head row no-margin">
                        <h3 class="manage-doc-tit">
                           
                        </h3>
                    </div>
                    @include('common.notify')                         
                     <div class="alert alert-danger" id="failed" style="display: none;">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <h5 id="failed-message"></h5>
                    </div>
                    <div class="alert alert-success" id="success" style="display: none;">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <h5 id="success-message"></h5>
                    </div>
                     <div class="manage-doc-section-content border-top">
                     <div class="tab-content list-content">
                        <div class="list-view pad30 ">
                            @if(Setting::get('demo_mode', 0) == 1)
                                 <div class="col-md-12" style="height:50px;color:red;">
                                    ** Demo Mode : Use this card - CardNo:4000056655665556, MM:12, YY:20, CVV:123.
                                </div>
                            @endif
                            @if(config('constants.card') == 1) 
                            <a href="#" class="sub-right pull-right" data-toggle="modal" data-target="#" 
                             id="add_card" style="margin-right: 10px;margin-bottom: 10px;">Add Stripe Card</a>
                            @endif
                            @if(config('constants.paystack') == 1) 
                            <a href="#" class="sub-right pull-right" data-toggle="modal" data-target="#" 
                             id="add_card" style="margin-right: 10px;margin-bottom: 10px;">Add Paystack Card</a>
                            @endif 
                            <table class="earning-table table table-responsive">
                                <thead>
                                    <tr>
                                        <th>@lang('provider.card.type')</th>
                                        <th>@lang('provider.card.four')</th>    
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($cards)!='0')    
                                    @foreach($cards as $each)
                                        <tr>
                                            <td>{{ strtoupper($each->brand) }}</td>
                                            <td>**** **** **** {{ $each->last_four }}</td>
                                            
                                                <form action="{{url('provider/card/destroy')}}" method="POST" class="pull-right">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="card_id" value="{{$each->card_id}}">
                                                    <td>
                                                        <button onclick='return confirm("@lang('user.card.delete_msg')")' type="submit" class="btn btn-sm" >@lang('user.card.delete')</button>
                                                    </td>
                                                </form>
                                        </tr>
                                    @endforeach
                                    @else
                                        <tr align="center">
                                            <td colspan="3">@lang('provider.card.notfound')</td>
                                       </tr>
                                    @endif
                                </tbody>

                            </table>
                        </div>
                     </div>
                     </div>
               
                </div>
            </div>
        </div>
    </div>
    <!-- Add Card Modal -->
    <div id="add-card-modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" >
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">@lang('user.card.add_card')</h4>
          </div>
            <form id="card-form" action="{{ url('provider/card/store') }}" method="POST" >
                {{ csrf_field() }}
            <div class="modal-body">
            <div class="row no-margin" id="card-payment">
                <div class="form-group col-md-12 col-sm-12">
                    <label>@lang('user.card.card_no')</label>
                    <input data-stripe="number" data-paystack="number" id="number" type="text" onkeypress="return isNumberKey(event);" required autocomplete="off" maxlength="25" class="form-control" placeholder="@lang('user.card.card_no')">
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label>@lang('user.card.month')</label>
                    <input type="text" onkeypress="return isNumberKey(event);" maxlength="2" required autocomplete="off" class="form-control" data-stripe="exp-month" id="expiryMonth" data-paystack="expiryMonth" placeholder="MM" required>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label>@lang('user.card.year')</label>
                    <input type="text" onkeypress="return isNumberKey(event);" id="expiryYear" data-paystack="expiryYear" maxlength="2" required autocomplete="off" data-stripe="exp-year" class="form-control" placeholder="YY" required>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label>@lang('user.card.cvv')</label>
                    <input type="text" data-stripe="cvc" id="cvv" data-paystack="cvv" onkeypress="return isNumberKey(event);" required autocomplete="off" maxlength="4" class="form-control" placeholder="@lang('user.card.cvv')" required>
                </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-default" id="btn-add-card" data-paystack="submit">
            @lang('user.card.add_card')</button>
          </div>
        </form>
        </div>
      </div>
    </div>

    <div id="pin-modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" >
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Pin Code Verification</h4>
          </div>
            <form id="pin-form" method="POST" >
                {{ csrf_field() }}
          <div class="modal-body">
            <div class="row no-margin" id="card-payment">
                <div class="form-group col-md-12 col-sm-12">
                    <label>Pin Code</label>
                    <input data-stripe="number" type="password" data-paystack="pin"  onkeypress="return isNumberKey(event);" required autocomplete="off" maxlength="8" class="form-control" placeholder="Pin Code" id="pin">
                </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-default" id="btn-pin" data-paystack="submit">Continue</button>
          </div>
        </form>
        </div>
      </div>
    </div>    


    <div id="otp-modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" >
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Otp Number Verification</h4>
          </div>
            <form id="otp-form" method="POST" >
                {{ csrf_field() }}
          <div class="modal-body">
            <div class="row no-margin" id="card-payment">
                <div class="form-group col-md-12 col-sm-12">
                    <label>Otp Number</label>
                    <input data-stripe="number" type="text" id="otp" data-paystack="otp" onkeypress="return isNumberKey(event);" required autocomplete="off" maxlength="8" class="form-control" placeholder="Otp Number" id="otp-number">
                </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-default" id="btn-otp" data-paystack="submit">Continue</button>
          </div>
        </form>
        </div>
      </div>
    </div>

    <div id="phone-modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" >
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Phone Verification</h4>
          </div>
            <form id="phone-form" method="POST" >
                {{ csrf_field() }}
          <div class="modal-body">
            <div class="row no-margin" id="card-payment">
                <div class="form-group col-md-12 col-sm-12">
                    <label>Phone Number</label>
                    <input data-stripe="number" type="text" id="phone" data-paystack="phone" onkeypress="return isNumberKey(event);" required autocomplete="off" maxlength="18" class="form-control" placeholder="Phone Number" id="phone-number">
                </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-default" id="btn-phone" data-paystack="submit">Continue</button>
          </div>
        </form>
        </div>
      </div>
    </div>
@endsection
@if(config('constants.paystack') == 1)   
    @section('scripts')
 
    <!-- <script type="text/javascript" src="https://js.stripe.com/v2/"></script> -->
    <!-- <script src="{{asset('asset/js/paystack.js')}}"></script> -->
    <!-- <script src="{{asset('asset/js/paystack_main.js')}}"></script> -->
    <script src="https://js.paystack.co/v1/paystack.js"></script>
    <script src="{{asset('asset/js/paystack_verification.js')}}"></script>

    <script type="text/javascript">

        $('#add_card').on('click', function(evt){
            //payWithPaystack("{{config('constants.paystack_public_key', '')}}", "{{Auth::user()->email}}");
            var email = "{{Auth::user()->email}}";
            var amount = 50*100;
            // then we initialize the transaction on the backend
            startTransactionOnBackend(email, amount);
            // clean up
            $("#processing").show();
        });

        
// Be careful changing their name or the data they accept 

        function reportErrorToBackend(error){
            // we are reporting only the error here. in real life
            // you will want to collect a little more information
            $.ajax({
                type: "POST",
                url: 'report',
                data: {error: error}
            });
        }

        function startTransactionOnBackend(email, amount){
            $.ajax({
                type: "POST",
                url: "{{ route('paystackaccess') }}",
                data: {_token: '{{ csrf_token() }}',
                        email: email, 
                        amount: amount,
                        metadata:{}},
                success: function (access_code) {
                    startPaystack(access_code);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log("There was an error getting an accesscode", xhr.responseText);
                    reportErrorToBackend(xhr.responseText);
                    // clean up
                    $("#processing").hide();
                    //$("#add-card-modal").modal();
                    $('#failed').show();
                    $('#failed-message').html(xhr.responseText);
                }
            });
        }


        function verifyTransactionOnBackend(reference){

            $.ajax({
                type: "GET",
                url: "{{route('paystackverify')}}?trxref=" + reference,
                success: function (gateway_response) {
                    var returnVal = JSON.parse(gateway_response);
                    var data = returnVal.response;
                    if(returnVal.status == "success")
                    {
                        var card_id = data.id;
                        var last4 = data.authorization.last4;
                        var auth_code = data.authorization.authorization_code;
                        var card_name = data.authorization.brand;
                        $.ajax({
                            type: "POST",
                            url: "{{route('card.provide_cardsave')}}",
                            data: {
                                _token: '{{ csrf_token() }}',
                                card_id: card_id,
                                last4: last4,
                                auth_code: auth_code,
                                card_name: card_name 
                            },
                            dataType: "text",
                            success:function(data)
                            {
                               if(data.indexOf("ok") > 0 || data == "ok")
                               {
                                    $('#success').show();
                                    $('#success-message').html('Card have been added successfully.');
                                    setTimeout(function()
                                    { 
                                        window.location.reload();
                                    }, 3000);
                               } else
                               {
                                    $('#failed').show();
                                    $('#failed-message').html('The Operation failed.');
                                    setTimeout(function()
                                    { 
                                        window.location.reload();
                                    }, 3000);
                               }
                            }
                        });
                    }
                    //$('#success').show();
                    //$('#success-message').html('Card add successed.');
                },
                error: function (xhr, ajaxotpions, thrownError) {
                    console.log("There was an error verifying "+reference, xhr.responseText);
                    reportErrorToBackend(xhr.responseText);
                    $("#verify-error").html(xhr.responseText);
                    setTimeout(function()
                    { 
                        window.location.reload();
                    }, 3000);
                }
            });
        }      
    </script>
    @endsection
@endif

@if(config('constants.card') == 1)   
    @section('scripts')
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

    <script type="text/javascript">
        Stripe.setPublishableKey("{{ config('constants.stripe_publishable_key', '')}}");

     var stripeResponseHandler = function (status, response) {
        var $form = $('#card-form');

        if (response.error) {
            // Show the errors on the form
            $form.find('.payment-errors').text(response.error.message);
            $form.find('button').prop('disabled', false);
            alert(response.error.message);

        } else {
            // token contains id, last4, and card type
            var token = response.id;

            // Insert the token into the form so it gets submitted to the server
            $form.append($('<input type="hidden" id="stripeToken" name="stripe_token" />').val(token));

            jQuery($form.get(0)).submit();
            $("#add-card-modal").modal('toggle');
        }
    };
            
        $('#card-form').submit(function (e) {            
            if ($('#stripeToken').length == 0)
            {
                var $form = $(this);
                $form.find('button').prop('disabled', true);                
                Stripe.card.createToken($form, stripeResponseHandler);
                return false;
            }
        });

        $('#add_card').on('click', function(evt){
            $('#add-card-modal').modal();
        });
                
    </script>
    @endsection
@endif
    <script type="text/javascript">
        function isNumberKey(evt)
        {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
                return false;

            return true;
        }

        function set_default(id)
        {
            $.ajax({
                method : 'POST',
                url : '{{ url('provider/card/set') }}',
                data : '_token={{csrf_token()}}&id='+id,
                success:function(html)
                {
                    if(html=='success')
                    {
                        alert('Successfully made changes');
                    }
                    else{
                        alert('Something Went wrong'); 
                    }
                }

            })
        }
    </script>