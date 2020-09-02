<div class="col-md-3">
    <div class="dash-left">
        <div class="user-img">
            <div class="pro-img" style="background-image: url({{ Auth::guard('provider')->user()->avatar ? : asset('asset/img/provider.jpg') }});"></div>
            <h4>{{ Auth::guard('provider')->user()->first_name }} {{ Auth::guard('provider')->user()->last_name }}</h4>
        </div>
        <div class="side-menu">
             <ul>
                <li><a class="active" href="{{ route('provider.index') }}">@lang('provider.profile.home')</a></li>
                <li><a href="{{ route('provider.trips') }}">@lang('provider.profile.my_trips')</a></li>
                <li><a href="{{ route('provider.profile.show') }}">@lang('provider.profile.profile')</a></li>
                <li>
                    <a href="{{ url('/provider/logout') }}"
                        onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                        @lang('provider.profile.logout')
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>