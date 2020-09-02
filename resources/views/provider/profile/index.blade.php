@extends('provider.layout.app')

@section('content')
<div class="pro-dashboard-head">
    <div class="container">
        <a href="#" class="pro-head-link active">@lang('provider.profile.profile')</a>
        <a href="{{ route('provider.documents.index') }}" class="pro-head-link">@lang('provider.profile.manage_documents')</a>
        <a href="{{ route('provider.location.index') }}" class="pro-head-link">@lang('provider.profile.update_location')</a>        
        <a href="{{route('provider.wallet.transation')}}" class="pro-head-link">@lang('provider.profile.wallet_transaction')</a>
        @if(config('constants.card')==1)
            <a href="{{ route('provider.cards') }}" class="pro-head-link">@lang('provider.card.list')</a>
        @endif
        <a href="{{ route('provider.transfer') }}" class="pro-head-link">@lang('provider.profile.transfer')</a>
        @if(config('constants.referral')==1)
            <a href="{{ route('provider.referral') }}" class="pro-head-link">@lang('provider.profile.refer_friend')</a>
        @endif
    </div>
</div>
<!-- Pro-dashboard-content -->
<div class="pro-dashboard-content gray-bg">
    <div class="profile">
        <!-- Profile head -->
        
        <div class="container">
            <div class="profile-head white-bg row no-margin">
                @include('common.notify')
                <div class="prof-head-left col-lg-2 col-md-2 col-sm-3 col-xs-12">
                    <div class="new-pro-img bg-img" style="background-image: url({{ Auth::guard('provider')->user()->avatar ? asset('storage/'.Auth::guard('provider')->user()->avatar) : asset('asset/img/provider.jpg') }});"></div>
                </div> 

                <div class="prof-head-right col-lg-10 col-md-10 col-sm-9 col-xs-12"">
                    <h3 class="prof-name">{{ Auth::guard('provider')->user()->first_name }} {{ Auth::guard('provider')->user()->last_name }}</h3>
                    <p class="board-badge">{{ strtoupper(Auth::guard('provider')->user()->status) }}</p>
                </div>
            </div>
        </div>

        <!-- Profile-content -->
        <div class="profile-content gray-bg pad50">
            <div class="container">
                <div class="row no-margin">
                    <div class="col-lg-7 col-md-7 col-sm-8 col-xs-12 no-padding">
                        <form class="profile-form" action="{{route('provider.profile.update')}}" method="POST" enctype="multipart/form-data" role="form">
                            {{csrf_field()}}
                            <!-- Prof-form-sub-sec -->
                            <div class="prof-form-sub-sec">
                                <div class="row no-margin">
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-left-padding">
                                        <div class="form-group">
                                            <label>@lang('provider.profile.first_name')</label>
                                            <input type="text" class="form-control" placeholder="@lang('provider.profile.first_name')" name="first_name" value="{{ Auth::guard('provider')->user()->first_name }}" data-validation="alphanumeric" data-validation-allowing=" -" data-validation-error-msg="@lang('provider.profile.first_name') @lang('provider.profile.error_msg')">
                                        </div>
                                    </div>
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-right-padding">
                                        <div class="form-group">
                                            <label>@lang('provider.profile.last_name')</label>
                                            <input type="text" class="form-control" placeholder="@lang('provider.profile.last_name')" name="last_name" value="{{ Auth::guard('provider')->user()->last_name }}" data-validation="alphanumeric" data-validation-allowing=" -" data-validation-error-msg="@lang('provider.profile.last_name') @lang('provider.profile.error_msg')">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="prof-sub-col prof-1 col-xs-12">
                                        <div class="form-group">
                                            <label>@lang('provider.profile.avatar')</label>
                                            <input type="file" class="form-control" name="avatar">
                                        </div>
                                    </div>
                                </div>

                                <div class="row no-margin">
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-left-padding">
                                        <div class="form-group">
                                            <label>@lang('provider.profile.phone')</label>
                                            <input type="text" class="form-control" id="phone_number" required placeholder="Contact Number" name="mobile" value="{{ Auth::guard('provider')->user()->mobile }}" data-validation="custom length" data-validation-length="10-15" data-validation-regexp="^([0-9\+]+)$" data-validation-error-msg="@lang('provider.profile.error_phone')" disabled="disabled">
                                            <div id="phone_number_container" style="display: none;">
                                                <div class="prof-sub-col col-sm-3 no-left-padding">
                                                <input type="text" class="form-control col-sm-2" name="country_code" value="" placeholder="+91" >
                                                </div>
                                                <div class="prof-sub-col col-sm-9 no-left-padding">
                                                <input type="text" class="form-control col-sm-2" name="phone_number" value="" >
                                                </div>
                                            </div>
                                            <div id="mobile_verfication"></div>
                                        </div>
                                    </div>
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-left-padding">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <a class="btn btn-block btn-primary update-link update-mobile" style="margin-top: 0;">@lang('provider.profile.change_mobile')</a>
                                            <a class="btn btn-block btn-primary update-link verify-mobile" style="margin-top: 0; display: none;">@lang('provider.profile.verify')</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="prof-sub-col col-sm-12 col-xs-12 no-right-padding">
                                        <div class="form-group no-margin">
                                            <label for="exampleSelect1">@lang('provider.profile.language')</label>
                                            @php($language=get_all_language())
                                            <select class="form-control" name="language" id="language">
                                                @if(Auth::guard('provider')->user()->profile)
                                                    @foreach($language as $lkey=>$lang)
                                                        <option value="{{$lkey}}" @if(Auth::guard('provider')->user()->profile->language==$lkey) selected @endif>{{$lang}}</option>
                                                    @endforeach
                                                @else
                                                   @foreach($language as $lkey=>$lang)
                                                        <option value="{{$lkey}}">{{$lang}}</option>
                                                    @endforeach     
                                                @endif    
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of prof-sub-sec -->

                            <!-- Prof-form-sub-sec -->
                            <div class="prof-form-sub-sec border-top">
                                <div class="form-group">
                                    <label>@lang('provider.profile.address')</label>
                                    <input type="text" class="form-control" placeholder="@lang('provider.profile.address')" name="address" value="{{ Auth::guard('provider')->user()->profile ? Auth::guard('provider')->user()->profile->address : "" }}">
                                    <input type="text" class="form-control" placeholder="@lang('provider.profile.full_address')" style="border-top: none;" name="address_secondary" value="{{ Auth::guard('provider')->user()->profile ? Auth::guard('provider')->user()->profile->address_secondary : "" }}">
                                </div>

                                <!-- <div class="row no-margin">
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-left-padding">
                                        <div class="form-group no-margin">
                                            <label>@lang('provider.profile.city')</label>
                                            <input type="text" class="form-control" placeholder="@lang('provider.profile.city')" name="city" value="{{ Auth::guard('provider')->user()->profile ? Auth::guard('provider')->user()->profile->city : "" }}">
                                        </div>
                                    </div>
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-right-padding">
                                        <div class="form-group">
                                            <label>@lang('provider.profile.country')</label>
                                            <select class="form-control" name="country">
                                                <option value="US">United States</option>
                                            </select>
                                        </div>
                                    </div>
                                </div> -->

                                <div class="row no-margin">
                                    <!-- <div class="prof-sub-col col-sm-6 col-xs-12 no-left-padding">
                                        <div class="form-group no-margin">
                                            <label>@lang('provider.profile.pin')</label>
                                            <input type="text" class="form-control" placeholder="@lang('provider.profile.pin')" name="postal_code" value="{{ Auth::guard('provider')->user()->profile ? Auth::guard('provider')->user()->profile->postal_code : "" }}">
                                        </div>
                                    </div> -->
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-left-padding">
                                        <div class="form-group">
                                            <label>@lang('provider.profile.service_type')</label>
                                            <select class="form-control" name="service_type" data-validation="required">
                                                <option value="">Select Service</option>
                                                @foreach(get_all_service_types() as $type)
                                                    <option @if(Auth::guard('provider')->user()->service->service_type->id == $type->id) selected="selected" @endif value="{{$type->id}}">{{$type->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-right-padding">
                                        <div class="form-group no-margin">
                                            <label>@lang('provider.profile.car_number')</label>
                                            <input type="text" class="form-control" placeholder="@lang('provider.profile.car_number')" name="service_number" value="{{ Auth::guard('provider')->user()->service->service_number ? Auth::guard('provider')->user()->service->service_number : "" }}" data-validation="alphanumeric" data-validation-allowing=" -" data-validation-error-msg="@lang('provider.profile.car_number') @lang('provider.profile.error_msg')">
                                        </div>
                                    </div>
                                </div>

                                <div class="row no-margin">                                    
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-left-padding">
                                        <div class="form-group">
                                            <label>@lang('provider.profile.car_model')</label>
                                            <input type="text"  placeholder="@lang('provider.profile.car_model')" class="form-control" name="service_model" value="{{ Auth::guard('provider')->user()->service->service_model ? Auth::guard('provider')->user()->service->service_model : "" }}" data-validation="alphanumeric" data-validation-allowing=" -" data-validation-error-msg="@lang('provider.profile.car_model') @lang('provider.profile.error_msg')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of prof-sub-sec -->

                            <div class="row no-margin">
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-left-padding">
                                        <div class="form-group">
                                            <label>@lang('provider.profile.qr_code')</label>
                                            <img src="{{asset(Auth::guard('provider')->user()->qrcode_url)}}">

                                            <div id="mobile_verfication"></div>
                                        </div>
                                    </div>
                            <!-- Prof-form-sub-sec -->
                            <div class="prof-form-sub-sec border-top">
                                <div class="col-xs-12 col-md-6 col-md-offset-3">
                                    <button type="submit" class="btn btn-block btn-primary update-link">@lang('provider.profile.update')</button>
                                </div>
                            </div>
                            <!-- End of prof-sub-sec -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
<script type="text/javascript">
    $.validate();       
</script>
<script src="https://sdk.accountkit.com/en_US/sdk.js"></script>
<script>

  $('.update-mobile').on('click', function() {
    smsLogin();
  });

  $('.verify-mobile').on('click', function() {
    verify();
  });

  AccountKit_OnInteractive = function(){
    AccountKit.init(
      {
        appId: {{Config::get('constants.facebook_app_id')}}, 
        state:"state", 
        version: "{{Config::get('constants.facebook_app_version')}}",
        fbAppEventsEnabled:true
      }
    );
  };


   // login callback
  function loginCallback(response) {
    if (response.status === "PARTIALLY_AUTHENTICATED") {
      var code = response.code;
      var csrf = response.state;

      $.post("{{route('account.kit')}}",{ code : code }, function(data){
        $('#phone_number').attr('readonly',true);
        $('#phone_number').attr('disabled',false);
        $('#phone_number').val('+'+data.phone.country_prefix+data.phone.national_number);
        $('.verify-mobile').text("@lang('user.profile.verified')");
        $('.verify-mobile').removeClass('verify-mobile');

        $('#phone_number_container').hide();
        $('#phone_number').show();
        $('#phone_number').attr('disabled',false);
        $('#mobile_verfication').html("");
        //console.log(data);
      });
    }
    else if (response.status === "NOT_AUTHENTICATED") {
      // handle authentication failure
      $('#mobile_verfication').html("<p class='helper'> * Authentication Failed </p>");
    }
    else if (response.status === "BAD_PARAMS") {
      // handle bad parameters
    }
  }

  // phone form submission handler
  function smsLogin() {
    $('#phone_number_container').show();
    $('#phone_number').hide();
    $('.update-mobile').hide();
    $('.verify-mobile').show();
  }

  function verify() {
    $('#phone_number').attr('disabled',false);
    $('.update-mobile').text("@lang('provider.profile.verify')");

    var countryCode = $('input[name=country_code]').val();
    var phoneNumber = $('input[name=phone_number]').val();

    $.post("{{url('/provider/verify-credentials')}}",{ _token: '{{csrf_token()}}', id : '{{ \Auth::guard('provider')->user()->id }}', mobile : countryCode+phoneNumber }).done(function(data){ 
        $('#mobile_verfication').html("<p class='helper'> Please Wait... </p>");

        AccountKit.login(
          'PHONE', 
          {countryCode: countryCode, phoneNumber: phoneNumber}, // will use default values if not specified
          loginCallback
        );
    })
    .fail(function(xhr, status, error) {
        $('#mobile_verfication').html("<p class='helper'> "+xhr.responseJSON.message+" </p>");
    });

    /*var countryCode = "+91";
    var phoneNumber = document.getElementById("phone_number").value;*/

    /*$('#mobile_verfication').html("<p class='helper'> Please Wait... </p>");

    */
  }

</script>
@endsection