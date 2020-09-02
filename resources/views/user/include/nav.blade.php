<div class="col-md-3">
    <div class="dash-left">
        <div class="user-img">
            <?php $profile_image = img(Auth::user()->picture); ?>
            <div class="pro-img" style="background-image: url({{$profile_image}});"></div>
            <h4>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</h4>
            <!-- <h4>Referral Code : {{Auth::user()->referral_unique_id}}</h4> -->
        </div>
        <div class="side-menu">
             <ul>
                <li><a href="{{url('dashboard')}}">@lang('user.dashboard')</a></li>
                <!-- <li class="dropdown"><a class="dropdown-toggle">@lang('user.profile.settings')</a>
                  <ul class="dropdown-menu">
                      <li><a href="#">Home</a></li>
                      <li><a href="#">Last Destination</a></li>
                      <li><a style="background-color: black;color: #fff;">Choose Language</a>
                              <ul >
                                  <li><a><input type="radio" name="lange" value="en"> ENGLISH</a></li>
                                  <li><a><input type="radio" name="lange" value="ar"> AERABIC</a></li>
                                  <li><a><input type="radio" name="lange" value="fr"> FRENCH</a></li>
                                  <li><a><input type="radio" name="lange" value="pr"> PORTUGESE</a></li>
                              </ul>
                      </li>
                  </ul>

                </li> -->
                <li><a href="{{ url('/notifications') }}">@lang('user.notifications')</a></li>
                <li><a href="{{url('trips')}}">@lang('user.my_trips')</a></li>
                <li><a href="{{url('upcoming/trips')}}">@lang('user.upcoming_trips')</a></li>
                <li><a href="{{url('profile')}}">@lang('user.profile.profile')</a></li>
                <li><a href="{{url('change/password')}}">@lang('user.profile.change_password')</a></li>
                <li><a href="{{url('/payment')}}">@lang('user.payment')</a></li>
                <!-- <li>
				<a href="{{ url('/notification') }}" class="waves-effect waves-light">
					@lang('user.profile.notify')</a>
			    </li> -->
                <li><a href="{{url('/wallet')}}">@lang('user.my_wallet') <span class="pull-right">{{currency(Auth::user()->wallet_balance)}}</span></a></li>
                @if(config('constants.referral') == 1)
                  <li><a href="{{url('/referral')}}">@lang('user.referral')</a></li>
                @endif  
                <li><a href="{{ url('/logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">@lang('user.profile.logout')</a></li>
                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
            </ul>
        </div>
    </div>
</div>