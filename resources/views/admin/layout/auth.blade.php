<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Title -->
    <title>{{ config('constants.site_title', 'Tranxit') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ config('constants.site_icon') }}"/>

    <!-- Vendor CSS 
    <link rel="stylesheet" href="{{asset('main/vendor/bootstrap4/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('main/vendor/themify-icons/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('main/vendor/font-awesome/css/font-awesome.min.css')}}">

    <link rel="stylesheet" href="{{asset('main/assets/css/core.css')}}">-->
    <link href="{{asset('asset/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('main/vendor/bootstrap4/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('main/assets/css/style.css')}}">
    

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
    .header-top {
    position: relative;
    z-index: 99999;
    background: #1d1c1c;
    font-size: 12px;
    width: 100%;
    padding: 11px 20px;
    color: #fff;
    text-align: center;
  }
  .header-top p {
    color: #fff;
    margin-bottom: 0px;
  }
  span.cross-icon.pull-right i {
    color: #fff;
  }
  span.cross-icon.pull-right i:hover {
    cursor: pointer;
  }
  .fixedmenu {
    top: 0px;
  }
  
  .header-top a {
    color: #fff;
  }
    </style>
</head>
<body>
@include('user.notification')
    <div id="form">

        @yield('content')

    </div>
        <!-- Vendor JS 
        <script type="text/javascript" src="{{asset('main/vendor/jquery/jquery-1.12.3.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('main/vendor/tether/js/tether.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('main/vendor/bootstrap4/js/bootstrap.min.js')}}"></script>-->
        <script type="text/javascript" src="{{asset('main/vendor/jquery/jquery-1.11.3.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('main/vendor/bootstrap4/bootstrap.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('main/assets/js/admin-login.js')}}"></script>
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
        <script>
$(document).ready(function()
{
    $('span.cross-icon').click(function()
    {
        $('.header-top').slideUp();
        $('.navbar').css('top','0px');
    });

});
$(window).scroll(function()
{
    if($(this).scrollTop()>50)
    {
        $('header>nav.navbar.navbar-fixed-top').addClass('fixedmenu')
    }
    else{
        $('header>nav.navbar.navbar-fixed-top').removeClass('fixedmenu');
    }
});
</script>
    </body>
</html>
