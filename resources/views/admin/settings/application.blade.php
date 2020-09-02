@extends('admin.layout.base')

@section('title', 'Site Settings ')

@section('content')

<div class="content-area py-1">
   
    <div class="container-fluid">
	<div class="box box-block bg-white">
	@if(Setting::get('demo_mode', 0) == 1)
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : @lang('admin.demomode')
                </div>
                @endif
          
		<div class="bd-example bd-example-tabs" role="tabpanel">
  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-expanded="true">General</a>
    </li>
    <li class="nav-item">
      <a class="nav-link " id="social-tab" data-toggle="tab" href="#social" role="tab" aria-controls="social" aria-expanded="false">Comp. Profile Links</a>
    </li>
	<li class="nav-item">
      <a class="nav-link " id="socialConfiguration-tab" data-toggle="tab" href="#socialConfiguration" role="tab" aria-controls="socialConfiguration" aria-expanded="false">Social Link Config</a>
    </li>
    <li class="nav-item">
      <a class="nav-link " id="provider-tab" data-toggle="tab" href="#provider" role="tab" aria-controls="provider" aria-expanded="false">Search Algorithm</a>
    </li>
    <li class="nav-item">
      <a class="nav-link " id="api-tab" data-toggle="tab" href="#api" role="tab" aria-controls="api" aria-expanded="false">Map&SMS Config</a>
    </li>
    <li class="nav-item">
      <a class="nav-link " id="mailconfig-tab" data-toggle="tab" href="#mailconfig" role="tab" aria-controls="mailconfig" aria-expanded="false">Mail Config</a>
    </li>
    <li class="nav-item">
      <a class="nav-link " id="pushnotification-tab" data-toggle="tab" href="#pushnotification" role="tab" aria-controls="pushnotification" aria-expanded="false">Push Notification</a>
    </li>

    <li class="nav-item">
      <a class="nav-link " id="others-tab" data-toggle="tab" href="#others" role="tab" aria-controls="others" aria-expanded="false">Others</a>
    </li>
   
  </ul>
  <div class="tab-content" id="myTabContent">
    <div role="tabpanel" class="tab-pane fade active in" id="general" aria-labelledby="home-tab" aria-expanded="true">
    	<div class="form-box row">
    		<div class="col-md-9">
    <form class="form-horizontal" action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" role="form" autocomplete="off">
            	{{csrf_field()}}

				<div class="form-group row">
					<label for="site_title" class="col-xs-3 col-form-label">@lang('admin.setting.Site_Name')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.site_title', 'Tranxit')  }}" name="site_title" required id="site_title" placeholder="Site Name">
					</div>
				</div>

				<div class="form-group row">
					<label for="site_logo" class="col-xs-3 col-form-label">@lang('admin.setting.Site_Logo')</label>
					<div class="col-xs-9">
						@if(config('constants.site_logo')!='')
	                    <img style="height: 90px; margin-bottom: 15px;" src="{{ config('constants.site_logo', asset('logo-black.png')) }}">
	                    @endif
						<input type="file" accept="image/*" name="site_logo" class="dropify form-control-file" id="site_logo" aria-describedby="fileHelp">
					</div>
				</div>


				<div class="form-group row">
					<label for="site_icon" class="col-xs-3 col-form-label">@lang('admin.setting.Site_Icon')</label>
					<div class="col-xs-9">
						@if(config('constants.site_icon')!='')
	                    <img style="height: 90px; margin-bottom: 15px;" src="{{ config('constants.site_icon') }}">
	                    @endif
						<input type="file" accept="image/*" name="site_icon" class="dropify form-control-file" id="site_icon" aria-describedby="fileHelp">
					</div>
				</div>

				<div class="form-group row">
					<label for="timezone" class="col-xs-3 col-form-label">@lang('admin.setting.timezone')</label>
					<div class="col-xs-9">
						<select class="form-control" id="timezone" name="timezone" autocomplete="off">
							<option value="UTC" @if(Config::get('constants.timezone') == 'UTC') selected @endif>Select Timezone</option>
							<option value="Asia/Kolkata" @if(Config::get('constants.timezone') == 'Asia/Kolkata') selected @endif>Asia/Kolkata</option>
						</select>
					</div>
				</div>

                <div class="form-group row">
					<label for="contact_number" class="col-xs-3 col-form-label">@lang('admin.setting.Contact_Number')</label>
					<div class="col-xs-9">
						<input class="form-control" type="number" value="{{ config('constants.contact_number', '911')  }}" name="contact_number" required id="contact_number" placeholder="Contact Number">
					</div>
				</div>

				<div class="form-group row">
					<label for="contact_email" class="col-xs-3 col-form-label">@lang('admin.setting.Contact_Email')</label>
					<div class="col-xs-9">
						<input class="form-control" type="email" value="{{ config('constants.contact_email', '')  }}" name="contact_email" required id="contact_email" placeholder="Contact Email">
					</div>
				</div>

                <div class="form-group row">
					<label for="sos_number" class="col-xs-3 col-form-label">@lang('admin.setting.SOS_Number')</label>
					<div class="col-xs-9">
						<input class="form-control" type="number" value="{{ config('constants.sos_number', '911')  }}" name="sos_number" required id="sos_number" placeholder="SOS Number">
					</div>
				</div>

				<div class="form-group row">
                    <label for="tax_percentage" class="col-xs-3 col-form-label">@lang('admin.setting.Copyright_Content')</label>
                    <div class="col-xs-9">
                        <input class="form-control" type="text" value="{{ config('constants.site_copyright', '&copy; '.date('Y').' Appoets') }}" name="site_copyright" id="site_copyright" placeholder="Site Copyright">
                    </div>
                </div>

                <div class="form-group row">
					<label for="" class="col-xs-2 col-form-label"></label>
					@if( Setting::get('demo_mode', 0) == 0)

					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.setting.Update_Site_Settings')</button>
					</div>
					@endif
				</div>
                </form>
            </div>
        </div>
    </div>
    <div class="tab-pane fade " id="social" role="tabpanel" aria-labelledby="social-tab" aria-expanded="false">
     <div class="form-box row">
    		<div class="col-md-8">
    			<form class="form-horizontal" action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}

    			<div class="form-group row">
					<label for="store_link_android" class="col-xs-3 col-form-label">@lang('admin.setting.Android_user_link')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.store_link_android_user', '')  }}" name="store_link_android_user"  id="store_link_android_user" placeholder="@lang('admin.setting.Android_user_link')">
					</div>
				</div>

				<div class="form-group row">
					<label for="store_link_ios" class="col-xs-3 col-form-label">@lang('admin.setting.Android_provider_link')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.store_link_android_provider', '')  }}" name="store_link_android_provider"  id="store_link_android_provider" placeholder="@lang('admin.setting.Android_provider_link')">
					</div>
				</div>

				<div class="form-group row">
					<label for="store_link_ios" class="col-xs-3 col-form-label">@lang('admin.setting.Ios_user_Link')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.store_link_ios_user', '')  }}" name="store_link_ios_user"  id="store_link_ios_user" placeholder="@lang('admin.setting.Ios_user_Link')">
					</div>
				</div>

				<div class="form-group row">
					<label for="store_link_ios" class="col-xs-3 col-form-label">@lang('admin.setting.Ios_provider_Link')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.store_link_ios_provider', '')  }}" name="store_link_ios_provider"  id="store_link_ios_provider" placeholder="@lang('admin.setting.Ios_provider_Link')">
					</div>
				</div>

				<div class="form-group row">
					<label for="store_link_ios" class="col-xs-3 col-form-label">@lang('admin.setting.Facebook_Link')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.store_facebook_link', '')  }}" name="store_facebook_link"  id="store_facebook_link" placeholder="@lang('admin.setting.Facebook_Link')">
					</div>
				</div>

				<div class="form-group row">
					<label for="store_link_ios" class="col-xs-3 col-form-label">@lang('admin.setting.Twitter_Link')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.store_twitter_link', '')  }}" name="store_twitter_link"  id="store_twitter_link" placeholder="@lang('admin.setting.Twitter_Link')">
					</div>
				</div>

				<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					@if( Setting::get('demo_mode', 0) == 0)

					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.setting.Update_Site_Settings')</button>
					</div>
					@endif
				</div>
			</form>
    		</div>
    	</div>
    </div>

	<div class="tab-pane fade " id="socialConfiguration" role="tabpanel" aria-labelledby="socialConfiguration-tab" aria-expanded="false">
     <div class="form-box row">
    		<div class="col-md-8">
    			<form class="form-horizontal" action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
            	
            	<div class="form-group row">
					<label for="social_login" class="col-xs-3 col-form-label">@lang('admin.setting.Social_Login')</label>
					<div class="col-xs-9">
						<select class="form-control" id="social_login" name="social_login" autocomplete="off">
							<option value="1" @if(config('constants.social_login', 0) == 1) selected @endif>@lang('admin.Enable')</option>
							<option value="0" @if(config('constants.social_login', 0) == 0) selected @endif>@lang('admin.Disable')</option>
						</select>
					</div>
				</div>
            
				<div class="social_container" style=" @if(config('constants.social_login', 0) == 0) display: none;  @endif  ">
				<hr>
				<div class="form-group row">
					<label for="store_link_android" class="col-xs-3 col-form-label">@lang('admin.setting.facebook_client_id')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.facebook_client_id')  }}" name="facebook_client_id"  id="facebook_client_id" placeholder="@lang('admin.setting.facebook_client_id')">
					</div>
				</div>

				<div class="form-group row">
					<label for="store_link_android" class="col-xs-3 col-form-label">@lang('admin.setting.facebook_client_secret')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.facebook_client_secret')  }}" name="facebook_client_secret"  id="facebook_client_secret" placeholder="@lang('admin.setting.facebook_client_secret')">
					</div>
				</div>

				<div class="form-group row">
					<label for="store_link_android" class="col-xs-3 col-form-label">@lang('admin.setting.facebook_redirect')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.facebook_redirect')  }}" name="facebook_redirect"  id="facebook_redirect" placeholder="@lang('admin.setting.facebook_redirect')">
					</div>
				</div>

				<br><br>


				<div class="form-group row">
					<label for="store_link_android" class="col-xs-3 col-form-label">@lang('admin.setting.google_client_id')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.google_client_id')  }}" name="google_client_id"  id="google_client_id" placeholder="@lang('admin.setting.google_client_id')">
					</div>
				</div>

				<div class="form-group row">
					<label for="store_link_android" class="col-xs-3 col-form-label">@lang('admin.setting.google_client_secret')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.google_client_secret')  }}" name="google_client_secret"  id="google_client_secret" placeholder="@lang('admin.setting.google_client_secret')">
					</div>
				</div>

				<div class="form-group row">
					<label for="store_link_android" class="col-xs-3 col-form-label">@lang('admin.setting.google_redirect')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.google_redirect')  }}" name="google_redirect"  id="google_redirect" placeholder="@lang('admin.setting.google_redirect')">
					</div>
				</div>

				<hr>
				</div>
				
				<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					@if( Setting::get('demo_mode', 0) == 0)

					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.setting.Update_Site_Settings')</button>
					</div>
					@endif
				</div>
			</form>
    		</div>
    	</div>
    </div>

     <div class="tab-pane fade " id="provider" role="tabpanel" aria-labelledby="provider-tab" aria-expanded="false">
     	  <div class="form-box row">
    		<div class="col-md-10">
    			<form class="form-horizontal" action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
            	<div class="form-group row">
					<label for="provider_select_timeout" class="col-xs-3 col-form-label">@lang('admin.setting.Provider_Accept_Timeout') (Secs)</label>
					<div class="col-xs-9">
						<input class="form-control" type="number" value="{{ config('constants.provider_select_timeout', '60')  }}" name="provider_select_timeout" required id="provider_select_timeout" placeholder="Provider Timout">
					</div>
				</div>

				<div class="form-group row">
					<label for="provider_search_radius" class="col-xs-3 col-form-label">@lang('admin.setting.Provider_Search_Radius') (Kms)</label>
					<div class="col-xs-9">
						<input class="form-control" type="number" value="{{ config('constants.provider_search_radius', '100')  }}" name="provider_search_radius" required id="provider_search_radius" placeholder="Provider Search Radius">
					</div>
				</div>


            
				<div class="form-group row">
					<label for="distance" class="col-xs-3 col-form-label">@lang('admin.setting.distance')</label>
					<div class="col-xs-9">
						<select name="distance" class="form-control">
							<option value="Kms" @if(config('constants.distance') == 'Kms') selected @endif>Kms</option>
							<option value="Miles" @if(config('constants.distance') == 'Miles') selected @endif>Miles</option>
						</select>	
					</div>
				</div>


            	<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					@if( Setting::get('demo_mode', 0) == 0)

					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.setting.Update_Site_Settings')</button>
					</div>
					@endif
				</div>
            	</form>
    		</div>
    	</div>
    
    </div>


    <div class="tab-pane fade " id="api" role="tabpanel" aria-labelledby="api-tab" aria-expanded="false">
    <div class="form-box row">
    		<div class="col-md-10">
    			<form class="form-horizontal" action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
    			<div class="form-group row">

					<label for="map_key" class="col-xs-3 col-form-label">@lang('admin.setting.map_key')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.map_key')  }}" name="map_key" required id="map_key" placeholder="@lang('admin.setting.map_key')">
					</div>
				</div>

				<div class="form-group row">

					<label for="android_map_key" class="col-xs-3 col-form-label">@lang('admin.setting.android_map_key')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.android_map_key')  }}" name="android_map_key" required id="android_map_key" placeholder="@lang('admin.setting.android_map_key')">
					</div>
				</div>

				<div class="form-group row">

					<label for="ios_map_key" class="col-xs-3 col-form-label">@lang('admin.setting.ios_map_key')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.ios_map_key')  }}" name="ios_map_key" required id="ios_map_key" placeholder="@lang('admin.setting.ios_map_key')">
					</div>
				</div>

				<div class="form-group row">
					<label for="facebook_app_version" class="col-xs-3 col-form-label">@lang('admin.setting.fb_app_version')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.facebook_app_version')  }}" name="facebook_app_version" required id="facebook_app_version" placeholder="@lang('admin.setting.fb_app_version')">
					</div>
				</div>

				<div class="form-group row">
					<label for="facebook_app_id" class="col-xs-3 col-form-label">@lang('admin.setting.fb_app_id')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.facebook_app_id')  }}" name="facebook_app_id" required id="facebook_app_id" placeholder="@lang('admin.setting.fb_app_id')">
					</div>
				</div>

				<div class="form-group row">
					<label for="facebook_app_secret" class="col-xs-3 col-form-label">@lang('admin.setting.fb_app_secret')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.facebook_app_secret')  }}" name="facebook_app_secret" required id="facebook_app_secret" placeholder="@lang('admin.setting.fb_app_secret')">
					</div>
				</div>
				<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					@if( Setting::get('demo_mode', 0) == 0)

					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.setting.Update_Site_Settings')</button>
					</div>
					@endif
				</div>
			</form>
    		</div>
    	</div>
    </div>

	<div class="tab-pane fade " id="mailconfig" role="tabpanel" aria-labelledby="mailconfig-tab" aria-expanded="false">
   		<div class="form-box row">
    		<div class="col-md-10">
    			<form class="form-horizontal" action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}


				<div class="form-group row" id="mail_request">
					<label for="stripe_secret_key" class="col-xs-3 col-form-label"> @lang('admin.setting.send_mail') </label>
					<div class="col-xs-9">
						<div class="float-xs-left mr-1"><input @if(config('constants.send_email') == 1) checked  @endif  name="send_email" type="checkbox" class="js-switch" data-color="#43b968" id="mailchk"></div>
					</div>
				</div>

				<div class="form-group row hidemail">
					<label for="social_login" class="col-xs-3 col-form-label">@lang('admin.setting.mail_driver')</label>
					<div class="col-xs-9">
						<select class="form-control" name="mail_driver" required id="mail_driver">
							<option value="SMTP" @if(config('constants.mail_driver') == 'SMTP') selected @endif>@lang('admin.setting.smtp')</option>
							<option value="MAILGUN" @if(config('constants.mail_driver') == 'MAILGUN') selected @endif>@lang('admin.setting.mailgun')</option>
						</select>
					</div>
				</div>

				<div class="form-group row hidemail">
					<label for="mail_host" class="col-xs-3 col-form-label">@lang('admin.setting.mail_host')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.mail_host')  }}" name="mail_host" required id="mail_host" placeholder="@lang('admin.setting.mail_host')">
					</div>
				</div>

				<div class="form-group row hidemail">
					<label for="mail_port" class="col-xs-3 col-form-label">@lang('admin.setting.mail_port')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.mail_port')  }}" name="mail_port" required id="mail_port" placeholder="@lang('admin.setting.mail_port')">
					</div>
				</div>

				<div class="form-group row hidemail">
					<label for="mail_username" class="col-xs-3 col-form-label">@lang('admin.setting.mail_username')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.mail_username')  }}" name="mail_username" required id="mail_username" placeholder="@lang('admin.setting.mail_username')" >
					</div>
				</div>

				<div class="form-group row hidemail">
					<label for="mail_password" class="col-xs-3 col-form-label">@lang('admin.setting.mail_password')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.mail_password')  }}" name="mail_password" required id="mail_password" placeholder="@lang('admin.setting.mail_password')" >
					</div>
				</div>

				<div class="form-group row hidemail">
					<label for="mail_from_address" class="col-xs-3 col-form-label">@lang('admin.setting.mail_from_address')</label>
					<div class="col-xs-9">
						<input class="form-control" type="email" value="{{ config('constants.mail_from_address')  }}" name="mail_from_address" required id="mail_from_address" placeholder="@lang('admin.setting.mail_from_address')" >
					</div>
				</div>

				<div class="form-group row hidemail">
					<label for="mail_from_name" class="col-xs-3 col-form-label">@lang('admin.setting.mail_from_name')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.mail_from_name')  }}" name="mail_from_name" required id="mail_from_name" placeholder="@lang('admin.setting.mail_from_name')" >
					</div>
				</div>

				<div class="form-group row hidemail">
					<label for="mail_encryption" class="col-xs-3 col-form-label">@lang('admin.setting.mail_encryption')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.mail_encryption')  }}" name="mail_encryption" required id="mail_encryption" placeholder="@lang('admin.setting.mail_encryption')" >
					</div>
				</div>

				<div class="form-group row hidemail mail_domain">
					<label for="mail_domain" class="col-xs-3 col-form-label">@lang('admin.setting.mail_domain')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.mail_domain')  }}" name="mail_domain" id="mail_domain" placeholder="@lang('admin.setting.mail_domain')" >
					</div>
				</div>

				<div class="form-group row hidemail mail_secret">
					<label for="mail_secret" class="col-xs-3 col-form-label">@lang('admin.setting.mail_secret')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ config('constants.mail_secret') }}" name="mail_secret" id="mail_secret" placeholder="@lang('admin.setting.mail_secret')" >
					</div>
				</div>



				<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					@if( Setting::get('demo_mode', 0) == 0)

					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.setting.Update_Site_Settings')</button>
					</div>
					@endif
				</div>
            </form>
    		</div>
    	</div>
    </div>

    <div class="tab-pane fade " id="pushnotification" role="tabpanel" aria-labelledby="pushnotification-tab" aria-expanded="false">
   		<div class="form-box row">
    		<div class="col-md-10">
    			<form class="form-horizontal" action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}

				<div class="form-group row">
					<label for="mail_driver" class="col-xs-3 col-form-label">@lang('admin.setting.environment')</label>
					<div class="col-xs-9">
						<select name="environment" required id="environment" class="form-control">
							<option value="development">Development</option>
							<option value="production">Production</option>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label for="mail_driver" class="col-xs-3 col-form-label">@lang('admin.setting.ios_push_user_pem')</label>
					<div class="col-xs-9">
						<input class="form-control" type="file" value="" name="user_pem" id="user_pem" placeholder="@lang('admin.setting.ios_push_user_pem')">
					</div>
				</div>

				<div class="form-group row">
					<label for="mail_driver" class="col-xs-3 col-form-label">@lang('admin.setting.ios_push_provider_pem')</label>
					<div class="col-xs-9">
						<input class="form-control" type="file" value="" name="provider_pem" id="provider_pem" placeholder="@lang('admin.setting.ios_push_provider_pem')">
					</div>
				</div>

				<div class="form-group row">
					<label for="mail_host" class="col-xs-3 col-form-label">@lang('admin.setting.ios_push_password')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.ios_push_password')  }}" name="ios_push_password" required id="ios_push_password" placeholder="@lang('admin.setting.ios_push_password')">
					</div>
				</div>

				<div class="form-group row">
					<label for="mail_port" class="col-xs-3 col-form-label">@lang('admin.setting.android_push_key')</label>
					<div class="col-xs-9">
						<input class="form-control" type="text" value="{{ Config::get('constants.android_push_key')  }}" name="android_push_key" required id="android_push_key" placeholder="@lang('admin.setting.android_push_key')">
					</div>
				</div>


				<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					@if( Setting::get('demo_mode', 0) == 0)

					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.setting.Update_Site_Settings')</button>
					</div>
					@endif
				</div>
            </form>
    		</div>
    	</div>
    </div>

    <div class="tab-pane fade " id="others" role="tabpanel" aria-labelledby="others-tab" aria-expanded="false">
   		<div class="form-box row">
    		<div class="col-md-10">
    			<form class="form-horizontal" action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}


				<div class="form-group row" id="referral_request">
					<label for="stripe_secret_key" class="col-xs-3 col-form-label"> @lang('admin.setting.referral') </label>
					<div class="col-xs-9">
						<div class="float-xs-left mr-1"><input @if(config('constants.referral') == 1) checked  @endif  name="referral" type="checkbox" class="js-switch" data-color="#43b968" id="refchk"></div>
					</div>
				</div>

				<div class="form-group row hideref">
					<label for="referral_count" class="col-xs-3 col-form-label">@lang('admin.setting.referral_count')</label>
					<div class="col-xs-9">
						<input class="form-control" type="number" value="{{ config('constants.referral_count')  }}" name="referral_count" required id="referral_count" placeholder="@lang('admin.setting.referral_count')" min="0">
					</div>
				</div>

				<div class="form-group row hideref">
					<label for="referral_amount" class="col-xs-3 col-form-label">@lang('admin.setting.referral_amount')</label>
					<div class="col-xs-9">
						<input class="form-control" type="number" value="{{ config('constants.referral_amount')  }}" name="referral_amount" required id="referral_amount" placeholder="@lang('admin.setting.referral_amount')" min="0">
					</div>
				</div>				
				<div class="form-group row">
					<label for="stripe_secret_key" class="col-xs-3 col-form-label"> Manual Assigning </label>
					<div class="col-xs-9">
						<div class="float-xs-left mr-1"><input @if(config('constants.manual_request') == 1) checked  @endif  name="manual_request" type="checkbox" class="js-switch" data-color="#43b968"></div>
					</div>
				</div>

				

				<div class="form-group row" id="broadcast_request">
					<label id="unicast" for="broadcast_request" class="col-xs-3 col-form-label">Unicast </label>
					<div class="col-xs-1">
						<div class="float-xs-left mr-1"><input @if(config('constants.broadcast_request') == 1) checked  @endif  name="broadcast_request" id="bdchk" type="checkbox" class="js-switch" data-color="#43b968"></div>
					</div>
					<label id="broadcast" for="broadcast_request" class="col-xs-2 col-form-label"></label>
				</div>

				<div class="form-group row">
                    <label for="stripe_secret_key" class="col-xs-3 col-form-label">Ride Otp</label>
                    <div class="col-xs-9">
                        <div class="float-xs-left mr-1"><input  @if(config('constants.ride_otp') == 1) checked  @endif  name="ride_otp" type="checkbox" class="js-switch" data-color="#43b968"></div>
                    </div>
                </div>

				<div class="form-group row">
                            <label for="booking_prefix" class="col-xs-3 col-form-label">@lang('admin.payment.booking_id_prefix')</label>
                            <div class="col-xs-8">
                                <input class="form-control"
                                    type="text"
                                    value="{{ config('constants.booking_prefix', '0') }}"
                                    id="booking_prefix"
                                    name="booking_prefix"
                                    min="0"
                                    max="4"
                                    placeholder="Booking ID Prefix">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="base_price" class="col-xs-3 col-form-label">@lang('admin.payment.currency')
                                 ( <strong>{{ config('constants.currency', '$')  }} </strong>)
                            </label>
                            <div class="col-xs-8">
                                <select name="currency" class="form-control" required>
                                    <option @if(config('constants.currency') == "$") selected @endif value="$">US Dollar (USD)</option>
                                    <option @if(config('constants.currency') == "₹") selected @endif value="₹"> Indian Rupee (INR)</option>
                                    <option @if(config('constants.currency') == "د.ك") selected @endif value="د.ك">Kuwaiti Dinar (KWD)</option>
                                    <option @if(config('constants.currency') == "د.ب") selected @endif value="د.ب">Bahraini Dinar (BHD)</option>
                                    <option @if(config('constants.currency') == "﷼") selected @endif value="﷼">Omani Rial (OMR)</option>
                                    <option @if(config('constants.currency') == "£") selected @endif value="£">British Pound (GBP)</option>
                                    <option @if(config('constants.currency') == "€") selected @endif value="€">Euro (EUR)</option>
                                    <option @if(config('constants.currency') == "CHF") selected @endif value="CHF">Swiss Franc (CHF)</option>
                                    <option @if(config('constants.currency') == "ل.د") selected @endif value="ل.د">Libyan Dinar (LYD)</option>
                                    <option @if(config('constants.currency') == "B$") selected @endif value="B$">Bruneian Dollar (BND)</option>
                                    <option @if(config('constants.currency') == "S$") selected @endif value="S$">Singapore Dollar (SGD)</option>
                                    <option @if(config('constants.currency') == "₦") selected @endif value="₦">Nigerian Naira (NGN)</option>
                                    <option @if(config('constants.currency') == "AU$") selected @endif value="AU$"> Australian Dollar (AUD)</option>
                                </select>
                            </div>
                        </div>
						@if(Setting::get('demo_mode', 0) != 1)
				<div class="form-group row">
                    <label for="stripe_secret_key" class="col-xs-3 col-form-label">@lang('admin.db_backup')</label>
                    <div class="col-xs-9">
                        <div class="float-xs-left mr-1"> <a href="{{ route('admin.dbbackup') }}" class="btn btn-primary">@lang('admin.db_backup_btn') <i class="fa fa-download" aria-hidden="true"></i></a></div>
                    </div>
                  </div>
				  @endif

				<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					@if( Setting::get('demo_mode', 0) == 0)
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.setting.Update_Site_Settings')</button>
					</div>
                    @endif
				</div>
            </form>
    		</div>
    	</div>
    </div>    
  </div>
</div>
	</div>
</div>
</div>





@endsection

@section('scripts')
<script type="text/javascript">
switchbroadcast();
switchreferral();
switchmail();
switchMailDomain();
$('#broadcast_request').click(function(e) {
   switchbroadcast();
});
$('#referral_request').click(function(e) {
   switchreferral();
});
$('#mail_request').click(function(e) {
   switchmail();
   switchMailDomain();
});
$('#mail_driver').click(function(e) {
   switchMailDomain();
});


$('select[name=social_login]').on('change', function(e) {
	var value = $(this).val();
	$('.social_container').hide();
	$('.social_container input').prop('disabled', true);
	if(value == 1) {
		$('.social_container').show();
		$('.social_container input').prop('disabled', false);
	}
   
});

function switchbroadcast(){
	var isChecked = $("#bdchk").is(":checked");
	if(isChecked){
		$("#broadcast").text('Broadcast');
		$("#unicast").text('');
	}   
	else{
		$("#unicast").text('Unicast');
		$("#broadcast").text('');
	}
}

function switchreferral(){
	var isChecked = $("#refchk").is(":checked");
	if(isChecked){
		$(".hideref").show();
	}   
	else{
		$(".hideref").hide();
	}
}
function switchmail(){
	var isChecked = $("#mailchk").is(":checked");
	if(isChecked){
		$(".hidemail").find('input').attr('required', true);
		$(".hidemail").show();
	}   
	else{
		$(".hidemail").find('input').attr('required', false);
		$(".hidemail").hide();
	}
}
function switchMailDomain(){
	var mailDriver = $("#mail_driver").val();
	if(mailDriver =="SMTP"){
		$(".hidemail").find('#mail_secret').attr('required', false);
		$(".hidemail").find('#mail_domain').attr('required', false);
		$(".mail_secret").hide();
		$(".mail_domain").hide();
	}   
	else{
		$(".hidemail").find('#mail_secret').attr('required', true);
		$(".hidemail").find('#mail_domain').attr('required', true);
		$(".mail_secret").show();
		$(".mail_domain").show();
	}
}
</script>
@endsection