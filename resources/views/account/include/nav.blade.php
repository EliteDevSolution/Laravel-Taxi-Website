<div class="site-sidebar">
	<div class="custom-scroll custom-scroll-light">
		<ul class="sidebar-menu">
			<li class="menu-title">@lang('admin.include.account_dashboard')</li>
			<li>
				<a href="{{ route('account.dashboard') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-anchor"></i></span>
					<span class="s-text">@lang('admin.include.dashboard')</span>
				</a>
			</li>
			<li class="menu-title">@lang('admin.include.account_statements')</li>
			<li>
				<a href="{{ route('account.ride.statement') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-target"></i></span>
					<span class="s-text">@lang('admin.include.overall_ride_statments')</span>
				</a>
			</li>
			<li>
				<a href="{{ route('account.ride.statement.provider') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-target"></i></span>
					<span class="s-text">@lang('admin.include.provider_statement')</span>
				</a>
			</li>
			<li>
				<a href="{{ route('account.ride.statement.user') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-target"></i></span>
					<span class="s-text">@lang('admin.include.user_statement')</span>
				</a>
			</li>
			<li>
				<a href="{{ route('account.ride.statement.fleet') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-target"></i></span>
					<span class="s-text">@lang('admin.include.fleet_statement')</span>
				</a>
			</li>
			<!-- <li>
				<a href="{{ route('account.ride.statement.today') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-target"></i></span>
					<span class="s-text">@lang('admin.include.daily_statement')</span>
				</a>
			</li>
			<li>
				<a href="{{ route('account.ride.statement.monthly') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-target"></i></span>
					<span class="s-text">@lang('admin.include.monthly_statement')</span>
				</a>
			</li>
			<li>
				<a href="{{ route('account.ride.statement.yearly') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-target"></i></span>
					<span class="s-text">@lang('admin.include.yearly_statement')</span>
				</a>
			</li> -->
			<li class="menu-title">@lang('admin.include.account')</li>
			<li>
				<a href="{{ route('account.profile') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-user"></i></span>
					<span class="s-text">@lang('admin.include.account_settings')</span>
				</a>
			</li>
			<li>
				<a href="{{ route('account.password') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-exchange-vertical"></i></span>
					<span class="s-text">@lang('admin.include.change_password')</span>
				</a>
			</li>
			<li class="compact-hide">
				<a href="{{ url('/account/logout') }}"
                            onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
					<span class="s-icon"><i class="ti-power-off"></i></span>
					<span class="s-text">@lang('admin.include.logout')</span>
                </a>

                <form id="logout-form" action="{{ url('/account/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
			</li>
			
		</ul>
	</div>
</div>