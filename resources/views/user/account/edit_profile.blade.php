@extends('user.layout.base')

@section('title', 'Profile ')

@section('content')

<div class="col-md-9">
    <div class="dash-content">
        <div class="row no-margin">
            <div class="col-md-12">
                <h4 class="page-title">@lang('user.profile.edit_information')</h4>
            </div>
        </div>
            @include('common.notify')
        <div class="row no-margin edit-pro">
            <form action="{{url('profile')}}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
                <div class="col-md-12">
                    <label>@lang('user.profile.profile_picture')</label>
                    <div class="profile-img-blk">
                        <div class="img_outer">
                            <img class="profile_preview" id="profile_image_preview" src="{{img(Auth::user()->picture)}}" alt="your image"/>
                        </div>
                        <div class="fileUpload up-btn profile-up-btn">                   
                            <input type="file" id="profile_img_upload_btn" name="picture" class="upload" accept="image/x-png, image/jpeg"/>
                        </div>                             
                    </div> 
                </div>
                <div class="form-group col-md-6">
                    <label>@lang('user.profile.first_name')</label>
                    <input type="text" class="form-control" name="first_name" required placeholder="@lang('user.profile.first_name')" value="{{Auth::user()->first_name}}" data-validation="alphanumeric" data-validation-allowing=" -" data-validation-error-msg="First Name can only contain alphanumeric characters and . - spaces">
                </div>
                <div class="form-group col-md-6">
                    <label>@lang('user.profile.last_name')</label>
                    <input type="text" class="form-control" name="last_name" required placeholder="@lang('user.profile.last_name')" value="{{Auth::user()->last_name}}" data-validation-allowing=" -" data-validation-error-msg="Last Name can only contain alphanumeric characters and . - spaces">
                </div>

                <div class="form-group col-md-12">
                    <label>@lang('user.profile.email')</label>
                    <input type="email" class="form-control" placeholder="@lang('user.profile.email')" readonly value="{{Auth::user()->email}}">
                </div>

                <div class="row no-margin">
                    <div class="prof-sub-col col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>@lang('user.profile.mobile')</label>
                            <input type="text" class="form-control" id="phone_number" required placeholder="Contact Number" name="mobile" value="{{ Auth::user()->mobile }}" data-validation="custom length" data-validation-length="10-15" data-validation-regexp="^([0-9\+]+)$" data-validation-error-msg="Incorrect phone number" disabled="disabled">
                            <div id="phone_number_container" style="display: none;">
                                <div class="prof-sub-col col-sm-3 no-left-padding">
                                <input type="text" class="form-control col-sm-2"  name="country_code" value="" placeholder="+91" >
                                </div>
                                <div class="prof-sub-col col-sm-9 no-left-padding">
                                <input type="text" class="form-control col-sm-2"  name="phone_number" value="" >
                                </div>
                            </div>
                            <div id="mobile_verfication"></div>
                        </div>
                    </div>
                    <div class="prof-sub-col col-sm-6 col-xs-12 no-left-padding">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <a class="btn btn-block btn-primary update-link update-mobile" style="margin-top: 0;">@lang('user.profile.change_mobile')</a>
                            <a class="btn btn-block btn-primary update-link verify-mobile" style="margin-top: 0; display: none;">@lang('user.profile.verify')</a>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <label>@lang('user.profile.language')</label>
                    @php($language=get_all_language())
                    <select class="form-control" name="language" id="language">
                        @foreach($language as $lkey=>$lang)
                            <option value="{{$lkey}}" @if(Auth::user()->language==$lkey) selected @endif>{{$lang}}</option>
                        @endforeach
                    </select>
                </div>
              
                <div class="col-md-12 pull-right">
                    <button type="submit" class="form-sub-btn big">@lang('user.profile.save')</button>
                </div>
            </form>
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

    $.post("{{url('/user/verify-credentials')}}",{ _token: '{{csrf_token()}}', id : '{{ \Auth::user()->id }}', mobile : countryCode+phoneNumber }).done(function(data){ 
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