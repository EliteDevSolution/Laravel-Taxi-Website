<nav class="navbar navbar-inverse navbar-fixed-top" id="sidebar-wrapper" role="navigation">
    <ul class="nav sidebar-nav">
        <li>
            <a href="{{ route('provider.earnings') }}">@lang('provider.profile.partner_earnings')</a>
        </li>
        <!-- <li>
            <a href="#">Invite</a>
        </li> -->
        <li>
            <a href="{{ route('provider.profile.index') }}">@lang('provider.profile.profile')</a>
        </li>
        <!-- <li>
            <a href="{{config('constants.stripe_oauth_url')}}">Connect to Stripe</a>
        </li> -->
        <li>
				<a href="{{ url('provider/notifications') }}">
				@lang('provider.profile.notify')
				</a>
			</li>
        <li>
            <a href="{{ url('/provider/logout') }}"
                onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
                @lang('provider.profile.logout')
            </a>
            <form id="logout-form" action="{{ url('/provider/logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </li>
    </ul>
</nav>