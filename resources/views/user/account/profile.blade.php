@extends('user.layout.base')

@section('title', 'Profile ')

@section('content')

<div class="col-md-9">
    <div class="dash-content">
        <div class="row no-margin">
            <div class="col-md-12">
                <h4 class="page-title">@lang('user.profile.general_information')</h4>
            </div>
        </div>
            @include('common.notify')
        <div class="row no-margin">
            <form>
                <div class="col-md-6 pro-form">
                    <h5 class="col-md-6 no-padding"><strong>@lang('user.profile.first_name')</strong></h5>
                    <p class="col-md-6 no-padding">{{Auth::user()->first_name}}</p>                       
                </div>
                <div class="col-md-6 pro-form">
                    <h5 class="col-md-6 no-padding"><strong>@lang('user.profile.last_name')</strong></h5>
                    <p class="col-md-6 no-padding">{{Auth::user()->last_name}}</p>                       
                </div>
                <div class="col-md-6 pro-form">
                    <h5 class="col-md-6 no-padding"><strong>@lang('user.profile.email')</strong></h5>
                    <p class="col-md-6 no-padding">{{Auth::user()->email}}</p>
                </div>

                <div class="col-md-6 pro-form">
                    <h5 class="col-md-6 no-padding"><strong>@lang('user.profile.mobile')</strong></h5>
                    <p class="col-md-6 no-padding">{{Auth::user()->mobile}}</p>
                </div>
               
                <div class="col-md-6 pro-form">
                    <h5 class="col-md-6 no-padding"><strong>@lang('user.profile.wallet_balance')</strong></h5>
                    <p class="col-md-6 no-padding">{{currency(Auth::user()->wallet_balance)}}</p>
                </div>                  

                <div class="col-md-6 pro-form">
                    <h5 class="col-md-6 no-padding"><strong>@lang('user.profile.language')</strong></h5>
                    @php($language=get_all_language())
                    <p class="col-md-6 no-padding">
                        @if(!empty($language[Auth::user()->language]))
                        {{$language[Auth::user()->language]}}
                        @else
                        {{$language['en']}}
                        @endif</p>
                </div>
                <div class="col-md-6 pro-form">
                    <h5 class="col-md-6 no-padding"><strong>@lang('user.profile.country_code')</strong></h5>
                    <p class="col-md-6 no-padding">{{Auth::user()->country_code}}</p>
                </div> 
                <div class="col-md-6 pro-form">
                    <h5 class="col-md-6 no-padding"><strong>@lang('user.profile.qr_code')</strong></h5>
                    <img src="{{asset(Auth::user()->qrcode_url)}}">
                </div>  

                <div class="col-md-6 pro-form">
                    <a class="form-sub-btn" href="{{url('edit/profile')}}">@lang('user.profile.edit')</a>
                </div>

            </form>
        </div>

    </div>
</div>

@endsection