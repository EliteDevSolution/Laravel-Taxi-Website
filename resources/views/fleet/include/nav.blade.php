<div class="site-sidebar">
	<div class="custom-scroll custom-scroll-light">
		<ul class="sidebar-menu">
			<li class="menu-title">@lang('admin.include.fleet_dashboard')</li>
			<li>
				<a href="{{ route('fleet.dashboard') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-anchor"></i></span>
					<span class="s-text">@lang('admin.include.dashboard')</span>
				</a>
			</li>
			
			<li class="menu-title">@lang('admin.include.members')</li>
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-car"></i></span>
					<span class="s-text">@lang('admin.include.providers')</span>
				</a>
				<ul>
					<li><a href="{{ route('fleet.provider.index') }}">@lang('admin.include.list_providers')</a></li>
					<li><a href="{{ route('fleet.provider.create') }}">@lang('admin.include.add_new_provider')</a></li>
				</ul>
			</li>
			<li class="menu-title">@lang('admin.include.details')</li>
			<li>
				<a href="{{ route('fleet.map.index') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-map-alt"></i></span>
					<span class="s-text">@lang('admin.include.map')</span>
				</a>
			</li>
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-view-grid"></i></span>
					<span class="s-text">@lang('admin.include.ratings') &amp; @lang('admin.include.reviews')</span>
				</a>
				<ul>
					<li><a href="{{ route('fleet.provider.review') }}">@lang('admin.include.provider_ratings')</a></li>
				</ul>
			</li>
			<li class="menu-title">@lang('admin.include.requests')</li>
			<li>
				<a href="{{ route('fleet.requests.index') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-infinite"></i></span>
					<span class="s-text">@lang('admin.include.request_history')</span>
				</a>
			</li>
			<li>
				<a href="{{ route('fleet.requests.scheduled') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-palette"></i></span>
					<span class="s-text">@lang('admin.include.scheduled_rides')</span>
				</a>
			</li>
			<li class="menu-title">@lang('admin.include.transaction')</li>
			<li>
				<a href="{{ route('fleet.wallet') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-money"></i></span>
					<span class="s-text">@lang('admin.include.wallet')</span>
				</a>
			</li>
			
			@if(config('constants.card')==1)
				<li>	
					<a href="{{ route('fleet.cards') }}" class="waves-effect  waves-light">
						<span class="s-icon"><i class="ti-exchange-vertical"></i></span>
						<span class="s-text">@lang('admin.include.debit_card')</span>
					</a>
				</li>
			@endif		
			<li>	
				<a href="{{ route('fleet.transfer') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-exchange-vertical"></i></span>
					<span class="s-text">@lang('admin.include.transfer')</span>
				</a>
			</li>

			<li class="menu-title">@lang('admin.include.account')</li>
			<li>
				<a href="{{ route('fleet.profile') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-user"></i></span>
					<span class="s-text">@lang('admin.include.account_settings')</span>
				</a>
			</li>
			<li>
				<a href="{{ route('fleet.password') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-exchange-vertical"></i></span>
					<span class="s-text">@lang('admin.include.change_password')</span>
				</a>
			</li>
			<li class="compact-hide">
				<a href="{{ url('/fleet/logout') }}"
                            onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
					<span class="s-icon"><i class="ti-power-off"></i></span>
					<span class="s-text">@lang('admin.include.logout')</span>
                </a>

                <form id="logout-form" action="{{ url('/fleet/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
			</li>
			
		</ul>
	</div>
</div>