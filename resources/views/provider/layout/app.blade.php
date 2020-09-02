<?php
if(Auth::user()->status=='document'){   
   if(Route::current()->getName()!='provider.documents.index' && Route::current()->getName()!='provider.index'){
      header('location:/provider/documents');
      exit;
   }
}
if(Auth::user()->status=='card'){  
   if(Route::current()->getName()!='provider.cards' && Route::current()->getName()!='provider.index'){   
      header('Location:/provider/cards');
      exit;
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ config('constants.site_favicon', asset('favicon.ico')) }}" type="image/x-icon">
    <link rel="icon" href="{{ config('constants.site_favicon', asset('favicon.ico')) }}" type="image/x-icon">

    <title>@yield('title'){{ config('constants.site_title', 'Tranxit') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ config('constants.site_icon') }}"/>
    

    <!-- Styles -->
    <link href="{{ asset('asset/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('asset/css/slick.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('asset/css/slick-theme.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('asset/css/rating.css') }}" rel="stylesheet" type="text/css">
    @if(Config::get('app.locale')=='ar')
    <link href="{{ asset('asset/css/arabic_dashboard-style.css') }}" rel="stylesheet" type="text/css">
    @else
    <link href="{{ asset('asset/css/dashboard-style.css') }}" rel="stylesheet" type="text/css">
    @endif
    <link rel="stylesheet" href="{{ asset('main/assets/css/style_dialog.css')}}">
    @yield('styles')

    <link rel="stylesheet" href="{{asset('main/vendor/switchery/dist/switchery.min.css')}}">

    <!-- Scripts -->
    <script>
        var assetBaseUrl = "{{ asset('') }}storage/";
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>

    </script>
</head>
<body>
    
    <div id="wrapper">
        <div class="overlay" id="overlayer" data-toggle="offcanvas"></div>
        @php
        $route_name = Request::path();
        $allRouteDialog = config('guidelines.demo_mode_dialog.provider');
        $checkRouteDialog = isset($allRouteDialog[$route_name])?"true":"false";
        $tempVar = (Session::get($route_name))?Session::get($route_name):"false";
        if($checkRouteDialog =="true"){
        $dialog = $allRouteDialog[$route_name];
        }
        @endphp

        @if($checkRouteDialog =="true")
        <div id="demoModeDialog" class="demoModeDialogmodal">
            <div class="demoModeDialogmodal-content text-center">
                <span class="demoModeDialogclose">&times;</span>
                <div class="row demoModeDialogdis demoModeDialogdis1">
                    <p> {!! $dialog !!} </p>
            </div>
            </div>
         </div>
            @endif
        @include('provider.layout.partials.nav')
        <div id="page-content-wrapper">
            @include('provider.layout.partials.header')
            <div class="page-content" style="min-height: 700px;">
                <div class="pro-dashboard">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @include('provider.layout.partials.footer')

    <div id="modal-incoming"></div>

    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('asset/js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/js/jquery.mousewheel.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/js/jquery-migrate-1.2.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/js/slick.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/js/rating.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/js/dashboard-scripts.js') }}"></script>
    <script type="text/javascript" src="{{asset('main/vendor/switchery/dist/switchery.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/15.3.1/react.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/15.3.1/react-dom.js"></script>
    <script src="https://unpkg.com/babel-standalone@6.15.0/babel.min.js"></script>
    <script src="https://unpkg.com/react@16/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@16/umd/react-dom.production.min.js"></script>

    @if(Route::current()->getName()!='provider.cards')
        <script type="text/babel" src="{{ asset('asset/js/incomingg.js') }}"></script>
    @endif

    @yield('scripts')
    @if(Setting::get('demo_mode', 0) == 1)
        <!-- Start of LiveChat (www.livechatinc.com) code -->
        <script type="text/javascript">
            window.__lc = window.__lc || {};
            window.__lc.license = 8256261;
            (function() {
                var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
                lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
            })();
        </script>
        <!-- End of LiveChat code -->
    @endif
    <script type="text/javascript">


        $('body').on('keypress', '.numbers', function(e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });

        $('body').on('focus', '.price', function() {

            if ($(this).val() == "0.00") {
                $(this).val("");
            } else if (($(this).val()).length > 0) {
                $(this).val((parseFloat($(this).val())).toFixed(2));
            }
        }).on('focusout', '.price', function() {

            if ($(this).val() == "") {
                $(this).val("0.00");
            } else if (($(this).val()).length > 0) {
                $(this).val((parseFloat($(this).val())).toFixed(2));
            }
        });

        $('body').on('keypress', '.price', function(e) {
            if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
    
        </script>
        @if(($checkRouteDialog =="true") && (Setting::get('demo_mode', 0) == 1) && ($tempVar =="false"))
    {{Session::put($route_name,'true')}}
         <script type="text/javascript">
            var demoModeDialogmodal = document.getElementById('demoModeDialog');
            demoModeDialogmodal.style.display = "block";
            var demoModeDialogspan = document.getElementsByClassName("demoModeDialogclose")[0];
            demoModeDialogspan.onclick = function() 
            {
                demoModeDialogmodal.style.display = "none";
            }
            </script>
    @endif 
</body>
</html>