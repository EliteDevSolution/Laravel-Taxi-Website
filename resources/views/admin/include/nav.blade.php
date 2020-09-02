<div class="site-sidebar">
	<div class="custom-scroll custom-scroll-light">
		<ul class="sidebar-menu">
			@role('ADMIN|ACCOUNT')
			<li class="menu-title">@lang('admin.include.admin_dashboard')</li>
			<li>
				<a href="{{ route('admin.dashboard') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="fa fa-tachometer" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.dashboard')</span>
				</a>
			</li>
			@endrole

			@can('dispatcher-panel')
			<li>
				<a href="{{ route('admin.dispatcher.index') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="fa fa-transgender-alt" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.dispatcher_panel')</span>
				</a>
			</li>
			@endcan
			@can('dispute-list')
			
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-write" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.dispute_panel')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.dispute.index') }}">@lang('admin.include.dispute_type')</a></li>
					<li><a href="{{ route('admin.userdisputes') }}">@lang('admin.include.dispute_request')</a></li>
				</ul>
			</li>	

			@endcan
			@can('heat-map')
			<li>
				<a href="{{ route('admin.heatmap') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-map"></i></span>
					<span class="s-text">@lang('admin.include.heat_map')</span>
				</a>
			</li>
			@endcan
			@can('god-eye')
			<li>
				<a href="{{ route('admin.godseye') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="fa fa-eye"></i></span>
					<span class="s-text">God's Eye</span>
				</a>
			</li>
			@endcan
			
			@role('ADMIN')	
				<li class="menu-title">@lang('admin.include.members')</li>
			@endrole

			@can('role-list')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-users" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.roles')</span>
				</a>
				<ul>
					@can('role-list')<li><a href="{{ route('admin.role.index') }}">@lang('admin.include.role_types')</a></li>@endcan
					@can('sub-admin-list')<li><a href="{{ route('admin.sub-admins.index') }}">@lang('admin.include.sub_admins')</a></li>@endcan
				</ul>
			</li>
			@endcan

			@can('user-list')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-user" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.users')</span>
				</a>
				<ul>
					@can('user-list')<li><a href="{{ route('admin.user.index') }}">@lang('admin.include.list_users')</a></li>@endcan
					@can('user-create')<li><a href="{{ route('admin.user.create') }}">@lang('admin.include.add_new_user')</a></li>@endcan
				</ul>
			</li>
			@endcan

			@can('provider-list')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-server" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.providers')</span>
				</a>
				<ul>
					@can('provider-list')<li><a href="{{ route('admin.provider.index') }}">@lang('admin.include.list_providers')</a></li>@endcan
					@can('provider-create')<li><a href="{{ route('admin.provider.create') }}">@lang('admin.include.add_new_provider')</a></li>@endcan
				</ul>
			</li>
			@endcan

			@can('fleet-list')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><img src="{{asset('asset/img/boss.png')}}"></span>
					<span class="s-text">@lang('admin.include.fleet_owner')</span>
				</a>
				<ul>
					@can('fleet-list')<li><a href="{{ route('admin.fleet.index') }}">@lang('admin.include.list_fleets')</a></li>@endcan
					@can('fleet-create')<li><a href="{{ route('admin.fleet.create') }}">@lang('admin.include.add_new_fleet_owner')</a></li>@endcan
				</ul>
			</li>
			@endcan

			@can('dispatcher-list')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-share-square-o" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.dispatcher')</span>
				</a>
				<ul>
					@can('dispatcher-list')<li><a href="{{ route('admin.dispatch-manager.index') }}">@lang('admin.include.list_dispatcher')</a></li>@endcan
					@can('dispatcher-create')<li><a href="{{ route('admin.dispatch-manager.create') }}">@lang('admin.include.add_new_dispatcher')</a></li>@endcan
				</ul>
			</li>
			@endcan
			
			@can('account-manager-list')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><img src="{{asset('asset/img/account.png')}}"></span>
					<span class="s-text">@lang('admin.include.account_manager')</span>
				</a>
				<ul>
					@can('account-manager-list')<li><a href="{{ route('admin.account-manager.index') }}">@lang('admin.include.list_account_managers')</a></li>@endcan
					@can('account-manager-create')<li><a href="{{ route('admin.account-manager.create') }}">@lang('admin.include.add_new_account_manager')</a></li>@endcan
				</ul>
			</li>
			@endcan
			@can('dispute-manager-list')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><img src="{{asset('asset/img/account.png')}}"></span>
					<span class="s-text">@lang('admin.include.dispute_manager')</span>
				</a>
				<ul>
					@can('dispute-manager-list')<li><a href="{{ route('admin.dispute-manager.index') }}">@lang('admin.include.list_dispute_managers')</a></li>@endcan
					@can('dispute-manager-create')<li><a href="{{ route('admin.dispute-manager.create') }}">@lang('admin.include.add_new_dispute_manager')</a></li>@endcan
				</ul>
			</li>
			@endcan


			@role('ADMIN')
				<li class="menu-title">@lang('admin.include.accounts')</li>
			@endrole	
			@can('statements')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-book" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.statements')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.ride.statement') }}">@lang('admin.include.overall_ride_statments')</a></li>
					<li><a href="{{ route('admin.ride.statement.provider') }}">@lang('admin.include.provider_statement')</a></li>
					<li><a href="{{ route('admin.ride.statement.user') }}">@lang('admin.include.user_statement')</a></li>
					<li><a href="{{ route('admin.ride.statement.fleet') }}">@lang('admin.include.fleet_statement')</a></li>
					<!-- <li><a href="{{ route('admin.ride.statement.today') }}">@lang('admin.include.daily_statement')</a></li>
					<li><a href="{{ route('admin.ride.statement.monthly') }}">@lang('admin.include.monthly_statement')</a></li>
					<li><a href="{{ route('admin.ride.statement.yearly') }}">@lang('admin.include.yearly_statement')</a></li> -->
				</ul>
			</li>
			@endcan
			@can('settlements')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.transaction')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.providertransfer') }}">@lang('admin.include.provider_request')</a></li>
					<li><a href="{{ route('admin.fleettransfer') }}">@lang('admin.include.fleet_request')</a></li>
					<li><a href="{{ route('admin.transactions') }}">@lang('admin.include.all_transaction')</a></li>
				</ul>
			</li>
			@endcan

			@role('ADMIN')
				<li class="menu-title">@lang('admin.include.details')</li>
			@endrole	
			<!-- <li>
				<a href="{{ route('admin.map.index') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-map-alt"></i></span>
					<span class="s-text">@lang('admin.include.map')</span>
				</a>
			</li> -->
			
			@can('ratings')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-star-half-o" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.ratings') &amp; @lang('admin.include.reviews')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.user.review') }}">@lang('admin.include.user_ratings')</a></li>
					<li><a href="{{ route('admin.provider.review') }}">@lang('admin.include.provider_ratings')</a></li>
				</ul>
			</li>
			@endcan

			@role('ADMIN')
			<li class="menu-title">@lang('admin.include.rides')</li>
			@endrole

			@can('ride-history')
			<li>
				<a href="{{ route('admin.requests.index') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="fa fa-history" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.ride_history')</span>
				</a>
			</li>
			@endcan
			@can('schedule-rides')
			<li>
				<a href="{{ route('admin.requests.scheduled') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-palette"></i></span>
					<span class="s-text">@lang('admin.include.scheduled_rides')</span>
				</a>
			</li>
			@endcan

			@role('ADMIN')
			<li class="menu-title">@lang('admin.include.offer')</li>
			@endrole

			@can('promocodes-list')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-layout-tab"></i></span>
					<span class="s-text">@lang('admin.include.promocodes')</span>
				</a>
				<ul>
					@can('promocodes-list')<li><a href="{{ route('admin.promocode.index') }}">@lang('admin.include.list_promocodes')</a></li>@endcan
					@can('promocodes-create')<li><a href="{{ route('admin.promocode.create') }}">
					@lang('admin.include.add_new_promocode')</a></li>@endcan
				</ul>
			</li>
			@endcan
			
			@role('ADMIN')
			<li class="menu-title">@lang('admin.include.general')</li>
			@endrole

			@can('service-types-list')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><img src="{{asset('asset/img/support-service.png')}}"></span>
					<span class="s-text">@lang('admin.include.service_types')</span>
				</a>
				<ul>
					@can('service-types-list')<li><a href="{{ route('admin.service.index') }}">@lang('admin.include.list_service_types')</a></li>@endcan
					@can('service-types-create')<li><a href="{{ route('admin.service.create') }}">@lang('admin.include.add_new_service_type')</a></li>@endcan
					@can('peak-hour-list')<li><a href="{{ route('admin.peakhour.index') }}">@lang('admin.include.peakhour')</a></li>@endcan
				</ul>
			</li>
			@endcan
			@can('documents-list')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.documents')</span>
				</a>
				<ul>
					@can('documents-list')<li><a href="{{ route('admin.document.index') }}">@lang('admin.include.list_documents')</a></li>@endcan
					@can('documents-create')<li><a href="{{ route('admin.document.create') }}">@lang('admin.include.add_new_document')</a></li>@endcan
				</ul>
			</li>
			@endcan
			
			@can('notification-list')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-user" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.notify')</span>
				</a>
				<ul>
					@can('notification-list')<li><a href="{{ route('admin.notification.index') }}">@lang('admin.include.list_notifications')</a></li>@endcan
					@can('notification-create')<li><a href="{{ route('admin.notification.create') }}">@lang('admin.include.add_new_notification')</a></li>@endcan
				</ul>
			</li>
			@endcan
			@can('cancel-reasons-list')
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-user" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.reason')</span>
				</a>
				<ul>
					@can('cancel-reasons-list')<li><a href="{{ route('admin.reason.index') }}">@lang('admin.include.list_reasons')</a></li>@endcan
					@can('cancel-reasons-create')<li><a href="{{ route('admin.reason.create') }}">@lang('admin.include.add_new_reason')</a></li>@endcan
				</ul>
			</li>
			@endcan

			@role('ADMIN')
			<li class="menu-title">@lang('admin.include.payment_details')</li>
			@endrole

			@can('payment-history')
			<li>
				<a href="{{ route('admin.payment') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="fa fa-money" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.payment_history')</span>
				</a>
			</li>
			@endcan
			@can('payment-settings')
			<li>
				<a href="{{ route('admin.settings.payment') }}" class="waves-effect  waves-light">
					<span class="s-icon"><img src="{{asset('asset/img/credit-card.png')}}"></span>
					<span class="s-text">@lang('admin.include.payment_settings')</span>
				</a>
			</li>
			@endcan

			<li class="menu-title">@lang('admin.include.settings')</li>
			@can('site-settings')
			<li>
				<a href="{{ route('admin.settings') }}" class="waves-effect  waves-light">
					<span class="s-icon"><img src="{{asset('asset/img/repairing-service.png')}}"></span>
					<span class="s-text">@lang('admin.include.site_settings')</span>
				</a>
			</li>
			@endcan
			
			@role('ADMIN')
			<li class="menu-title">@lang('admin.include.others')</li>
			@endrole

			@can('cms-pages')
			<li>
				<a href="{{ route('admin.cmspages') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-file"></i></span>
					<span class="s-text">@lang('admin.include.cms_pages')</span>
				</a>
			</li>
			@endcan
			@can('help')
			<li>
				<a href="{{ route('admin.help') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-help"></i></span>
					<span class="s-text">@lang('admin.include.help')</span>
				</a>
			</li>
			@endcan
			@can('custom-push')
			<li>
				<a href="{{ route('admin.push') }}" class="waves-effect waves-light">
					<span class="s-icon"><img src="{{asset('asset/img/push-icon.png')}}"></span>
					<span class="s-text">@lang('admin.include.custom_push')</span>
				</a>
			</li>
			@endcan
			@can('transalations')
			<li>
				<a href="{{route('admin.translation') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-smallcap"></i></span>
					<span class="s-text">@lang('admin.include.translations')</span>
				</a>
			</li>
			@endcan
			@can('lost-item-list')
			<li>
				<a href="{{ route('admin.lostitem.index') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-write"></i></span>
					<span class="s-text">@lang('admin.include.lostitem')</span>
				</a>
			</li>
			@endcan

			@role('ADMIN')
			<li class="menu-title">@lang('admin.include.account')</li>
			@endrole

			@can('account-settings')
			<li>
				<a href="{{ route('admin.profile') }}" class="waves-effect  waves-light">
					<span class="s-icon"><img src="{{asset('asset/img/manager.png')}}"></span>
					<span class="s-text">@lang('admin.include.account_settings')</span>
				</a>
			</li>
			@endcan
			@can('change-password')
			<li>
				<a href="{{ route('admin.password') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="fa fa-key" aria-hidden="true"></i></span>
					<span class="s-text">@lang('admin.include.change_password')</span>
				</a>
			</li>
			@endcan
			<li class="compact-hide">
				<a href="{{ url('/admin/logout') }}"
                            onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
					<span class="s-icon"><i class="ti-power-off"></i></span>
					<span class="s-text">@lang('admin.include.logout')</span>
                </a>

                <form id="logout-form" action="{{ url('/admin/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
			</li>
			
		</ul>
	</div>
</div>