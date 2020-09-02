<div class="row footer no-margin">
    <div class="container">
        <div class="col-md-6 text-left">
            <p>{{ config('constants.site_copyright', '&copy; '.date('Y').' Appoets') }}</p>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12 text-right">
            <a href="{{config('constants.store_link_ios_provider','#')}}" target="_blank" class="app">
                <img src="{{asset('asset/img/appstore.png')}}">
            </a>
            <a href="{{config('constants.store_link_android_provider','#')}}" target="_blank" class="app">
                <img src="{{asset('asset/img/playstore.png')}}">
            </a>
        </div>
    </div>
</div>