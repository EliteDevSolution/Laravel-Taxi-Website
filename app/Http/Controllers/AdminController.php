<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Helpers\Helper;

use Auth;
use Setting;
use Exception;
use \Carbon\Carbon;
use App\Http\Controllers\SendPushNotification;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProviderResources\TripController;

use App\User;
use App\Fleet;
use App\Admin;
use App\Provider;
use App\UserPayment;
use App\ServiceType;
use App\UserRequests;
use App\ProviderService;
use App\UserRequestRating;
use App\UserRequestPayment;
use App\CustomPush;
use App\AdminWallet;
use App\ProviderWallet;
use App\FleetWallet;
use App\WalletRequests;
use App\ProviderDocument;
use ZipArchive;
use DB;
use Session;
use App\Services\ServiceTypes;
use QrCode;

class AdminController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('admin');
		$this->middleware('demo', ['only' => [
				'settings_store', 
				'settings_payment_store',
				'profile_update',
				'password_update',
				'send_push',
			]]);


        $this->middleware('permission:heat-map', ['only' => ['heatmap']]);
        $this->middleware('permission:god-eye', ['only' => ['godseye']]);
        $this->middleware('permission:ratings', ['only' => ['user_review','provider_review']]);
        $this->middleware('permission:site-settings', ['only' => ['settings', 'settings_store']]);
        $this->middleware('permission:db-backup', ['only' => ['DBbackUp']]);
        $this->middleware('permission:payment-history', ['only' => ['payment']]);
        $this->middleware('permission:payment-settings', ['only' => ['settings_payment', 'settings_payment_store']]);
        $this->middleware('permission:cms-pages', ['only' => ['cmspages', 'pages', 'pagesearch']]);
        $this->middleware('permission:custom-push', ['only' => ['push', 'send_push']]);
        $this->middleware('permission:help', ['only' => ['help']]);
        $this->middleware('permission:transalations', ['only' => ['translation']]);
        $this->middleware('permission:account-settings', ['only' => ['profile', 'profile_update']]);
        $this->middleware('permission:change-password', ['only' => ['password', 'password_update']]);


        $this->middleware('permission:statements', ['only' => ['statement', 'statement_provider', 'statement_range', 'statement_today', 'statement_monthly', 'statement_yearly']]);

        $this->middleware('permission:settlements', ['only' => ['transactions', 'transferlist']]);

		$this->perpage = config('constants.per_page', '10');
	}


	/**
	 * Dashboard.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function dashboard()
	{

		try{
		   
			Session::put('user', Auth::User());
		   
			/*$UserRequest = UserRequests::with('service_type')->with('provider')->with('payment')->findOrFail(83);

			echo "<pre>";
			print_r($UserRequest->toArray());exit;

			return view('emails.invoice',['Email' => $UserRequest]);*/


			$rides = UserRequests::has('user')->orderBy('id','desc')->get();
			$cancel_rides = UserRequests::where('status','CANCELLED');
			$scheduled_rides = UserRequests::where('status','SCHEDULED')->count();
			$user_cancelled = UserRequests::where('status','CANCELLED')->where('cancelled_by','USER')->count();
			$provider_cancelled = UserRequests::where('status','CANCELLED')->where('cancelled_by','PROVIDER')->count();
			$cancel_rides = $cancel_rides->count();
			$service = ServiceType::count();
			$fleet = Fleet::count();
			$provider = Provider::count();
			$revenue = UserRequestPayment::sum('total');
			$wallet['tips'] = UserRequestPayment::sum('tips');
			$providers = Provider::take(10)->orderBy('rating','desc')->get();
			$wallet['admin'] = AdminWallet::sum('amount');
			$wallet['provider_debit'] = Provider::select(DB::raw('SUM(CASE WHEN wallet_balance<0 THEN wallet_balance ELSE 0 END) as total_debit'))->get()->toArray();
			$wallet['provider_credit'] = Provider::select(DB::raw('SUM(CASE WHEN wallet_balance>=0 THEN wallet_balance ELSE 0 END) as total_credit'))->get()->toArray();
			$wallet['fleet_debit'] = Fleet::select(DB::raw('SUM(CASE WHEN wallet_balance<0 THEN wallet_balance ELSE 0 END) as total_debit'))->get()->toArray();
			$wallet['fleet_credit'] = Fleet::select(DB::raw('SUM(CASE WHEN wallet_balance>=0 THEN wallet_balance ELSE 0 END) as total_credit'))->get()->toArray();

			$wallet['admin_tax'] = AdminWallet::where('transaction_type',9)->sum('amount');
			$wallet['admin_commission'] = AdminWallet::where('transaction_type',1)->sum('amount');
			$wallet['admin_discount'] = AdminWallet::where('transaction_type',10)->sum('amount');
			$wallet['admin_referral'] = AdminWallet::where('transaction_type',12)->orWhere('transaction_type',13)->sum('amount');
			$wallet['admin_dispute'] = AdminWallet::where('transaction_type',16)->orWhere('transaction_type',17)->sum('amount');
			$wallet['peak_commission'] = AdminWallet::where('transaction_type',14)->sum('amount');
			$wallet['waiting_commission'] = AdminWallet::where('transaction_type',15)->sum('amount');
			
			return view('admin.dashboard',compact('providers','fleet','provider','scheduled_rides','service','rides','user_cancelled','provider_cancelled','cancel_rides','revenue', 'wallet'));
		}
		catch(Exception $e){
			return redirect()->route('admin.user.index')->with('flash_error','Something Went Wrong with Dashboard!');
		}
	}


	/**
	 * Heat Map.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */

	public function get_heatmap() {
		$rides = UserRequests::has('user')->orderBy('id','desc')->get();

		$data = [];

		foreach ($rides as $ride) {
			$data[] = ['lat' => $ride->s_latitude, 'lng' => $ride->s_longitude];
		}

		return $data;
	}

	public function heatmap()
	{
		return view('admin.heatmap');
	}

	public function godseye()
	{
			$providers = Provider::whereHas('trips', function ($query) {
				$query->where('status', 'STARTED');
			})->select('id', 'first_name', 'last_name', 'latitude', 'longitude')->get();

			return view('admin.godseye');
	}

	public function godseye_list(Request $request)
	{
	   try{

			if($request->status == 'STARTED' || $request->status == 'ARRIVED' || $request->status == 'PICKEDUP') {

				$status = $request->status;

				$providers = Provider::with(['service.service_type', 'trips'])->whereHas('trips', function ($query) use($status) {
					$query->where('status', $status);
				})->select('id', 'first_name', 'last_name', 'mobile', 'email', 'latitude', 'longitude')->get();

			} else if($request->status == 'ACTIVE') {

				$providers = Provider::with(['service.service_type', 'trips'])->whereHas('service', function ($query) {
					$query->where('status', 'active');
				})->select('id', 'first_name', 'last_name', 'mobile', 'email', 'latitude', 'longitude')->get();

			} else {

				$providers = Provider::with(['service.service_type', 'trips'])->whereHas('service', function ($query) {
					$query->whereIn('status', ['active', 'riding']);
				})->select('id', 'first_name', 'last_name', 'mobile', 'email', 'avatar', 'status', 'latitude', 'longitude')->get();
			
			}
			
			$locations = [];

			foreach ($providers as $provider) {
				$locations[] = ['name' => $provider->first_name." ".$provider->last_name, 'lat' =>  $provider->latitude, 'lng' => $provider->longitude, 'car_image' =>  asset('asset/img/cars/car.png')];
			}

			return response()->json(['providers' => $providers, 'locations' => $locations]);
		}
		catch(Exception $e){
			return response()->json(['error' => 'Something Went Wrong!']);
		}
	}

	/**
	 * Map of all Users and Drivers.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function map_index()
	{
		return view('admin.map.index');
	}

	/**
	 * Map of all Users and Drivers.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function map_ajax()
	{
		try {

			$Providers = Provider::where('latitude', '!=', 0)
					->where('longitude', '!=', 0)
					->with('service')
					->get();

			$Users = User::where('latitude', '!=', 0)
					->where('longitude', '!=', 0)
					->get();

			for ($i=0; $i < sizeof($Users); $i++) { 
				$Users[$i]->status = 'user';
			}

			$All = $Users->merge($Providers);

			return $All;

		} catch (Exception $e) {
			return [];
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function settings()
	{
		return view('admin.settings.application');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function settings_store(Request $request)
	{
		/*$this->validate($request,[
			'user_pem' => 'mimes:pem',
			'provider_pem' => 'mimes:pem',
		]);*/
		$config = base_path() . '/config/constants.php';

		if (!file_exists($config)) {
			$constantFile = fopen($config, "w");

			$data = "<?php 
				
				return array(
					'site_title' => 'Tranxit',
					'site_logo' => '',
					'site_email_logo' => '',
					'site_icon' => '',
					'site_copyright' => '&copy; 2019 Appoets',
					'provider_select_timeout' => '60',
					'provider_search_radius' => '100',
					'base_price' => '50',
					'price_per_minute' => '50',
					'tax_percentage' => '0',
					'manual_request' => '0',
					'broadcast_request' => '0',
					'default_lang' => 'en',
					'currency' => '$',
					'distance' => 'Kms',
					'scheduled_cancel_time_exceed' => '10',
					'price_per_kilometer' => '10',
					'commission_percentage' => '0',
					'store_link_android_provider' => '',
					'store_link_ios_user' => '',
					'store_link_ios_provider' => '',
					'version_ios_user' => '',
					'version_android_user' => '',
					'version_ios_provider' => '',
					'version_android_provider' => '',
					'store_facebook_link' => '',
					'store_twitter_link' => '',
					'daily_target' => '0',
					'surge_percentage' => '0',
					'schedule_time' => '20',
					'waiting_percentage' => '0',
					'peak_percentage' => '0',
					'surge_trigger' => '0',
					'demo_mode' => '0',
					'booking_prefix' => 'TRNX',
					'sos_number' => '911',
					'contact_number' => '',
					'contact_email' => 'admin@tranxit.com',
					'environment' => '',
					'ios_push_password' => '',
					'android_push_key' => '',
					'timezone' => 'Asia/Kolkata',
					'map_key' => '',
					'android_map_key' => '',
					'ios_map_key' => '',
					'social_login' => '0',
					'facebook_app_id' => '',
					'facebook_app_secret' => '',
					'facebook_app_version' => '',
					'facebook_redirect' => '',
					'facebook_client_id' => '',
					'facebook_client_secret' => '',
					'google_redirect' => '',
					'google_client_id' => '',
					'google_client_secret' => '',
					'cash' => '1',
					'card' => '0',
					'stripe_secret_key' => '',
					'stripe_publishable_key' => '',
                    'stripe_currency' => 'USD',
					'payumoney' => '0',
					'payumoney_environment' => 'test',
                    'payumoney_merchant_id' => '',
                    'payumoney_key' => '',
				    'payumoney_salt' => '',
				    'payumoney_auth' => '',
				    'paypal' => '0',
				    'paypal_environment' => 'sandbox',
                    'paypal_currency' => 'USD',
				    'paypal_client_id' => '',
				    'paypal_client_secret' => '',
				    'paypal_adaptive' => '0',
				    'paypal_adaptive_environment' => 'sandbox',
				    'paypal_username' => '',
				    'paypal_password' => '',
				    'paypal_secret' => '',
				    'paypal_certificate' => '',
				    'paypal_app_id' => '',
				    'paypal_adaptive_currency' => 'USD',
				    'paypal_email' => '',
				    'braintree' => '0',
				    'braintree_environment' => 'sandbox',
				    'braintree_merchant_id' => '',
				    'braintree_public_key' => '',
				    'braintree_private_key' => '',
				    'paytm' => '0',
                    'paytm_environment' => 'local',
                    'paytm_merchant_id' => '',
                    'paytm_merchant_key' => '',
                    'paytm_channel' => 'WEB',
                    'paytm_website' => 'WEBSTAGING',
                    'paytm_industry_type' => 'Retail',
                    'minimum_negative_balance' => '-10',
					'ride_otp' => '0',
					'fleet_commission_percentage' => '0',
					'provider_commission_percentage' => '0',
					'per_page' => '10',
					'send_email' => '0',
					'referral' => '0',
					'referral_count' => '0',
					'referral_amount' => '0',
					'track_distance' => '1',
					'mail_driver' => '',
					'mail_host' => '',
					'mail_port' => '',
					'mail_from_address' => '',
					'mail_from_name' => '',
					'mail_encryption' => '',
					'mail_username' => '',
					'mail_password' => '',
					'mail_domain' => '',
					'mail_secret' => ''
				);";

			fwrite($constantFile, $data);
			fclose($constantFile);
			chmod($config, 0777); 
		}

		$file = file_get_contents($config);
		$change_content = $file;



		if(!$request->has('ride_otp')) {
			$search_text = "'ride_otp' => '1'";
			$value_text =  "'ride_otp' => '0'";
			$change_content=str_replace($search_text, $value_text, $change_content);
		}

		if(!$request->has('send_email')) {
			$search_text = "'send_email' => '1'";
			$value_text =  "'send_email' => '0'";
			$change_content=str_replace($search_text, $value_text, $change_content);
		}

		if(!$request->has('referral')) {
			$search_text = "'referral' => '1'";
			$value_text =  "'referral' => '0'";
			$change_content=str_replace($search_text, $value_text, $change_content);
		}

		if(!$request->has('manual_request')) {
			$search_text = "'manual_request' => '1'";
			$value_text =  "'manual_request' => '0'";
			$change_content=str_replace($search_text, $value_text, $change_content);
		}

		if(!$request->has('broadcast_request')) {
			$search_text = "'broadcast_request' => '1'";
			$value_text =  "'broadcast_request' => '0'";
			$change_content=str_replace($search_text, $value_text, $change_content);
		}

		if($request->hasFile('user_pem')) {
			$request->file('user_pem')->storeAs(
				"apns/", 'user.pem'
			);
		}

		if($request->hasFile('provider_pem')) {
			$request->file('provider_pem')->storeAs(
				"apns/", 'provider.pem'
			);
		}

		file_put_contents($config, $change_content);

		foreach($request->except(['_token', 'site_logo', 'site_icon', 'site_email_logo', 'user_pem', 'provider_pem', 'paypal_certificate']) as $key => $value){

			$value = (trim($value) == 'on') ? '1' : trim($value);
			$searchfor = config('constants.'.$key);
			if($value != $searchfor) {
				$search_text = "'".$key."' => '".$searchfor."'";
				$value_text =  "'".$key."' => '".$value."'";
				$change_content=str_replace($search_text, $value_text, $change_content);
			}
			
			
			file_put_contents($config, $change_content);  
		}


		if($request->hasFile('site_icon')) {
			$site_icon = Helper::upload_picture($request->file('site_icon'));
			$search_text = "'site_icon' => '".config('constants.site_icon')."'";
			$value_text =  "'site_icon' => '".$site_icon."'";
			$change_content=str_replace($search_text, $value_text, $change_content);
		}

		if($request->hasFile('site_logo')) {
			$site_logo = Helper::upload_picture($request->file('site_logo'));
			$search_text = "'site_logo' => '".config('constants.site_logo')."'";
			$value_text =  "'site_logo' => '".$site_logo."'";
			$change_content=str_replace($search_text, $value_text, $change_content);
		}

		if($request->hasFile('site_email_logo')) {
			$site_email_logo = Helper::upload_picture($request->file('site_email_logo'));
			$search_text = "'site_logo' => '".config('constants.site_email_logo')."'";
			$value_text =  "'site_email_logo' => '".$site_email_logo."'";
			$change_content=str_replace($search_text, $value_text, $change_content);
		}

		file_put_contents($config, $change_content);
		\Artisan::call('config:cache');
		sleep(5);
		return back()->with('flash_success','Settings Updated Successfully');
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function settings_payment()
	{
		return view('admin.payment.settings');
	}

	/**
	 * Save payment related settings.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function settings_payment_store(Request $request)
	{
		if($request->has('card'))
		{
		$this->validate($request, [
				'card' => 'in:on',
				'cash' => 'in:on',
				'paystack' => 'in:on',
				'payumoney' => 'in:on',
				'paypal' => 'in:on',
				'paypal_adaptive' => 'in:on',
				'braintree' => 'in:on',
				'stripe_secret_key' => 'required_if:card,on|max:255',
				'stripe_publishable_key' => 'required_if:card,on|max:255',                
			]);
		}
		if($request->has('daily_target'))
		{
			$this->validate($request, [
				'card' => 'in:on',
				'cash' => 'in:on',
				'paystack' => 'in:on',
				'payumoney' => 'in:on',
				'paypal' => 'in:on',
				'paypal_adaptive' => 'in:on',
				'braintree' => 'in:on',
				'stripe_secret_key' => 'required_if:card,on|max:255',
				'stripe_publishable_key' => 'required_if:card,on|max:255',                
			]);
		}

		$config = base_path() . '/config/constants.php';

		$file = file_get_contents($config);
		$change_content = $file;

			if(!$request->has('daily_target'))
			{
				if(!$request->has('cash')) {
					$search_text = "'cash' => '1'";
					$value_text =  "'cash' => '0'";
					$change_content=str_replace($search_text, $value_text, $change_content);
				}

				if(!$request->has('card')) {
					$search_text = "'card' => '1'";
					$value_text =  "'card' => '0'";
					$change_content=str_replace($search_text, $value_text, $change_content);
				}

				if(!$request->has('paystack')) {
					$search_text = "'paystack' => '1'";
					$value_text =  "'paystack' => '0'";
					$change_content=str_replace($search_text, $value_text, $change_content);
				}

				if(!$request->has('payumoney')) {
					$search_text = "'payumoney' => '1'";
					$value_text =  "'payumoney' => '0'";
					$change_content=str_replace($search_text, $value_text, $change_content);
				}

				if(!$request->has('paypal')) {
					$search_text = "'paypal' => '1'";
					$value_text =  "'paypal' => '0'";
					$change_content=str_replace($search_text, $value_text, $change_content);
				}

				if(!$request->has('paypal_adaptive')) {
					$search_text = "'paypal_adaptive' => '1'";
					$value_text =  "'paypal_adaptive' => '0'";
					$change_content=str_replace($search_text, $value_text, $change_content);
				}

				if(!$request->has('braintree')) {
					$search_text = "'braintree' => '1'";
					$value_text =  "'braintree' => '0'";
					$change_content=str_replace($search_text, $value_text, $change_content);
				}

				if(!$request->has('paytm')) {
					$search_text = "'paytm' => '1'";
					$value_text =  "'paytm' => '0'";
					$change_content=str_replace($search_text, $value_text, $change_content);
				}

				if(($request->has('cash')==0 && $request->has('card')==0) && $request->has('paystack')==0 && $request->has('payumoney')==0 && $request->has('paypal')==0 && $request->has('paypal_adaptive')==0 && $request->has('braintree')==0 && $request->has('paytm')==0){

					return back()->with('flash_error','Atleast one payment mode must be enable.');
				}
		   }
		

		if($request->hasFile('paypal_certificate')) {
			$request->file('paypal_certificate')->storeAs(
				"certs/", 'cert_key.pem'
			);

			$search_text = "'paypal_certificate' => ''";
			$value_text =  "'paypal_certificate' => '".base_path()."/app/public/certs/cert_key.pem'";
			$change_content=str_replace($search_text, $value_text, $change_content);

			$search_text = "'paypal_certificate' => '".base_path()."/app/public/certs/cert_key.pem'";
			$value_text =  "'paypal_certificate' => '".base_path()."/app/public/certs/cert_key.pem'";
			$change_content=str_replace($search_text, $value_text, $change_content);
		}

		foreach($request->except(['_token', 'site_logo', 'site_icon', 'site_email_logo', 'user_pem', 'provider_pem', 'paypal_certificate']) as $key => $value){
			$value = (trim($value) == 'on') ? '1' : trim($value);
			$searchfor = config('constants.'.$key);
			if($value != $searchfor) {
				$search_text = "'".$key."' => '".$searchfor."'";
				$value_text =  "'".$key."' => '".$value."'";
				$change_content=str_replace($search_text, $value_text, $change_content);
			} 
		}

		file_put_contents($config, $change_content);
		sleep(5);
		return redirect('/admin/settings/payment')->with('flash_success','Settings Updated Successfully');
	}
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function profile()
	{
		return view('admin.account.profile');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function profile_update(Request $request)
	{
		
		$this->validate($request,[
			'name' => 'required|max:255',
			'email' => 'required|max:255|email|unique:admins,email,'.Auth::guard('admin')->user()->id.',id',
			'picture' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
		]);

		try{
			$admin = Auth::guard('admin')->user();
			$admin->name = $request->name;
			$admin->email = $request->email;
			$admin->language = $request->language;
			
			if($request->hasFile('picture')){
				$admin->picture = $request->picture->store('admin/profile');  
			}
			$admin->save();

			Session::put('user', Auth::User());

			return redirect()->back()->with('flash_success','Profile Updated');
		}

		catch (Exception $e) {
			 return back()->with('flash_error','Something Went Wrong!');
		}
		
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function password()
	{
		return view('admin.account.change-password');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function password_update(Request $request)
	{

		$this->validate($request,[
			'old_password' => 'required',
			'password' => 'required|min:6|confirmed',
		]);

		try {

		   $Admin = Admin::find(Auth::guard('admin')->user()->id);

			if(password_verify($request->old_password, $Admin->password))
			{
				$Admin->password = bcrypt($request->password);
				$Admin->save();

				return redirect()->back()->with('flash_success','Password Updated');
			}
		} catch (Exception $e) {
			 return back()->with('flash_error','Something Went Wrong!');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function payment()
	{
		try {
			 $payments = UserRequests::where('paid', 1)
					->has('user')
					->has('provider')
					->has('payment')
					->orderBy('user_requests.created_at','desc')
					->paginate($this->perpage);

			 $pagination=(new Helper)->formatPagination($payments);       
			
			return view('admin.payment.payment-history', compact('payments','pagination'));
		} catch (Exception $e) {
			 return back()->with('flash_error','Something Went Wrong!');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function help()
	{
		try {
			$str = '{"content":"<p>We&#39;d like to thank you for deciding to use our script. We enjoyed creating it and hope you enjoy using it to achieve your goals :)&nbsp;If you want something changed to suit&nbsp;your venture&#39;s needs better, drop us a line: info@tranxit.com<\/p>\r\n"}';
			$Data = json_decode($str, true);
			return view('admin.help', compact('Data'));
		} catch (Exception $e) {
			 return back()->with('flash_error','Something Went Wrong!');
		}
	}

	/**
	 * User Rating.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function user_review()
	{
		try {
			$Reviews = UserRequestRating::where('user_id', '!=', 0)->with('user', 'provider')->paginate($this->perpage);
			$pagination=(new Helper)->formatPagination($Reviews);
			return view('admin.review.user_review',compact('Reviews','pagination'));

		} catch(Exception $e) {
			return redirect()->route('admin.setting')->with('flash_error','Something Went Wrong!');
		}
	}

	/**
	 * Provider Rating.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function provider_review()
	{
		try {
			$Reviews = UserRequestRating::where('provider_id','!=',0)->with('user','provider')->paginate($this->perpage);
			$pagination=(new Helper)->formatPagination($Reviews);
			return view('admin.review.provider_review',compact('Reviews','pagination'));
		} catch(Exception $e) {
			return redirect()->route('admin.setting')->with('flash_error','Something Went Wrong!');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\ProviderService
	 * @return \Illuminate\Http\Response
	 */
	public function destory_provider_service($id){
		try {
			ProviderService::find($id)->delete();
			return back()->with('message', 'Service deleted successfully');
		} catch (Exception $e) {
			 return back()->with('flash_error','Something Went Wrong!');
		}
	}

	/**
	 * Testing page for push notifications.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function push_index()
	{

		$data = \PushNotification::app('IOSUser')
			->to('3911e9870e7c42566b032266916db1f6af3af1d78da0b52ab230e81d38541afa')
			->send('Hello World, i`m a push message');
		dd($data);
	}

	/**
	 * Testing page for push notifications.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function push_store(Request $request)
	{
		try {
			ProviderService::find($id)->delete();
			return back()->with('message', 'Service deleted successfully');
		} catch (Exception $e) {
			 return back()->with('flash_error','Something Went Wrong!');
		}
	}

	/**
	 * privacy.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */

	public function cmspages(){
		return view('admin.pages.static');
	}

	/**
	 * pages.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function pages(Request $request){
		$this->validate($request, [
				'types' => 'required|not_in:select',
			]);

		Setting::set($request->types, $request->content);
		Setting::save();

		return back()->with('flash_success', 'Content Updated!');
	}

	public function pagesearch($request){
		$value = Setting::get($request);        
		return $value;        
	}

	/**
	 * account statements.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function statement($type = '', $request = null){
        //  print_r($request->all());exit;
		try{
		   if((isset($request->provider_id) && $request->provider_id !=null) ||
			 (isset($request->user_id) && $request->user_id !=null) ||
			 (isset($request->fleet_id) && $request->fleet_id !=null) )
		   {
			$pages = trans('admin.include.overall_ride_statments');
			$listname = trans('admin.include.overall_ride_earnings');
			if($type == 'individual'){
				$pages = trans('admin.include.provider_statement');
				$listname = trans('admin.include.provider_earnings');
			}elseif($type == 'today'){
				$pages = trans('admin.include.today_statement').' - '. date('d M Y');
				$listname = trans('admin.include.today_earnings');
			}elseif($type == 'monthly'){
				$pages = trans('admin.include.monthly_statement').' - '. date('F');
				$listname = trans('admin.include.monthly_earnings');
			}elseif($type == 'yearly'){
				$pages = trans('admin.include.yearly_statement').' - '. date('Y');
				$listname = trans('admin.include.yearly_earnings');
			}elseif($type == 'range'){
				$pages = trans('admin.include.statement_from').' '.Carbon::createFromFormat('Y-m-d', $request->from_date)->format('d M Y').'  '.trans('admin.include.statement_to').' '.Carbon::createFromFormat('Y-m-d', $request->to_date)->format('d M Y');
			}

			if(isset($request->provider_id) && $request->provider_id !=null)
		    {
			$id = $request->provider_id;
			$statement_for ="provider";
			$rides = UserRequests::where('provider_id',$id)->with('payment')->orderBy('id','desc');
            $cancel_rides = UserRequests::where('status','CANCELLED')->where('provider_id',$id);
            $Provider = Provider::find($id);
            $revenue = UserRequestPayment::whereHas('request', function($query) use($id) {
                                    $query->where('provider_id', $id );
                                })->select(\DB::raw(
                                   'SUM(ROUND(provider_pay)) as overall, SUM(ROUND(provider_commission)) as commission' 
							   ));
			$page = $Provider->first_name."'s ".$pages;
			}elseif(isset($request->user_id) && $request->user_id !=null)
		    {
			$id = $request->user_id;
		    $statement_for ="user";
            $rides = UserRequests::where('user_id',$id)->with('payment')->orderBy('id','desc');
            $cancel_rides = UserRequests::where('status','CANCELLED')->where('user_id',$id);
            $user = User::find($id);
            $revenue = UserRequestPayment::whereHas('request', function($query) use($id) {
                                    $query->where('user_id', $id );
                                })->select(\DB::raw(
                                   'SUM(ROUND(total)) as overall' 
							   ));
			$page = $user->first_name."'s ".$pages;
			}
			else{
			$id = $request->fleet_id;
			$statement_for ="fleet";
            $rides = UserRequestPayment::where('fleet_id', $id)->whereHas('request', function($query) use($id) {
									$query->with('payment')->orderBy('id','desc');
								});
            $cancel_rides = UserRequestPayment::where('fleet_id', $id)->whereHas('request', function($query) use($id) {
															$query->where('status','CANCELLED');
														});
            $fleet = Fleet::find($id);
            $revenue = UserRequestPayment::where('fleet_id', $id)
                                                ->select(\DB::raw(
                                                'SUM(ROUND(fleet)) as overall' 
                                                ));	
			$page = $fleet->name."'s ".$pages;
			}

		   }else{
			$id ='';
			$statement_for ="";
			$page = trans('admin.include.overall_ride_statments');
			$listname = trans('admin.include.overall_ride_earnings');
			if($type == 'individual'){
				$page = trans('admin.include.provider_statement');
				$listname = trans('admin.include.provider_earnings');
			}elseif($type == 'today'){
				$page = trans('admin.include.today_statement').' - '. date('d M Y');
				$listname = trans('admin.include.today_earnings');
			}elseif($type == 'monthly'){
				$page = trans('admin.include.monthly_statement').' - '. date('F');
				$listname = trans('admin.include.monthly_earnings');
			}elseif($type == 'yearly'){
				$page = trans('admin.include.yearly_statement').' - '. date('Y');
				$listname = trans('admin.include.yearly_earnings');
			}elseif($type == 'range'){
				$page = trans('admin.include.statement_from').' '.Carbon::createFromFormat('Y-m-d', $request->from_date)->format('d M Y').'  '.trans('admin.include.statement_to').' '.Carbon::createFromFormat('Y-m-d', $request->to_date)->format('d M Y');
			}

			$rides = UserRequests::with('payment')->orderBy('id','desc');

			$cancel_rides = UserRequests::where('status','CANCELLED');
			$revenue = UserRequestPayment::select(\DB::raw(
						   'SUM(ROUND(fixed) + ROUND(distance)) as overall, SUM(ROUND(commision)) as commission' 
					   ));
		   }
		
		

			if($type == 'today'){

				$rides->where('created_at', '>=', Carbon::today());
				$cancel_rides->where('created_at', '>=', Carbon::today());
				$revenue->where('created_at', '>=', Carbon::today());

			}elseif($type == 'monthly'){

				$rides->where('created_at', '>=', Carbon::now()->month);
				$cancel_rides->where('created_at', '>=', Carbon::now()->month);
				$revenue->where('created_at', '>=', Carbon::now()->month);

			}elseif($type == 'yearly'){

				$rides->where('created_at', '>=', Carbon::now()->year);
				$cancel_rides->where('created_at', '>=', Carbon::now()->year);
				$revenue->where('created_at', '>=', Carbon::now()->year);

			}elseif ($type == 'range') {                
				if($request->from_date == $request->to_date) {
					$rides->whereDate('created_at', date('Y-m-d', strtotime($request->from_date)));
					$cancel_rides->whereDate('created_at', date('Y-m-d', strtotime($request->from_date)));
					$revenue->whereDate('created_at', date('Y-m-d', strtotime($request->from_date)));
				} else {
					$rides->whereBetween('created_at',[Carbon::createFromFormat('Y-m-d', $request->from_date),Carbon::createFromFormat('Y-m-d', $request->to_date)]);
					$cancel_rides->whereBetween('created_at',[Carbon::createFromFormat('Y-m-d', $request->from_date),Carbon::createFromFormat('Y-m-d', $request->to_date)]);
					$revenue->whereBetween('created_at',[Carbon::createFromFormat('Y-m-d', $request->from_date),Carbon::createFromFormat('Y-m-d', $request->to_date)]);
				}
			}

			$rides = $rides->paginate($this->perpage);
			if ($type == 'range'){
				$path='range?from_date='.$request->from_date.'&to_date='.$request->to_date;
				$rides->setPath($path);
			}
			$pagination=(new Helper)->formatPagination($rides);
			$cancel_rides = $cancel_rides->count();
			$revenue = $revenue->get();

			$dates['yesterday'] = Carbon::yesterday()->format('Y-m-d');
			$dates['today'] = Carbon::today()->format('Y-m-d');
			$dates['pre_week_start'] = date("Y-m-d", strtotime("last week monday"));
			$dates['pre_week_end'] = date("Y-m-d", strtotime("last week sunday"));
			$dates['cur_week_start'] = Carbon::today()->startOfWeek()->format('Y-m-d');
			$dates['cur_week_end'] = Carbon::today()->endOfWeek()->format('Y-m-d');
			$dates['pre_month_start'] = Carbon::parse('first day of last month')->format('Y-m-d');
			$dates['pre_month_end'] = Carbon::parse('last day of last month')->format('Y-m-d');
			$dates['cur_month_start'] = Carbon::parse('first day of this month')->format('Y-m-d');
			$dates['cur_month_end'] = Carbon::parse('last day of this month')->format('Y-m-d');
			$dates['pre_year_start'] = date("Y-m-d",strtotime("last year January 1st"));
			$dates['pre_year_end'] = date("Y-m-d",strtotime("last year December 31st"));
			$dates['cur_year_start'] = Carbon::parse('first day of January')->format('Y-m-d');
			$dates['cur_year_end'] = Carbon::parse('last day of December')->format('Y-m-d');
			$dates['nextWeek'] = Carbon::today()->addWeek()->format('Y-m-d');
			return view('admin.providers.statement', compact('rides','cancel_rides','revenue','pagination','dates','id','statement_for'))
					->with('page',$page)->with('listname',$listname);

		} catch (Exception $e) {
			return back()->with('flash_error','Something Went Wrong!');
		}
	}


	/**
	 * account statements today.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function statement_today(){
		return $this->statement('today');
	}

	/**
	 * account statements monthly.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function statement_monthly(){
		return $this->statement('monthly');
	}

	 /**
	 * account statements monthly.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function statement_yearly(){
		return $this->statement('yearly');
	}


	/**
	 * account statements range.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function statement_range(Request $request){
		return $this->statement('range', $request);
	}

	/**
	 * account statements.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function statement_provider(){

		try{

			$Providers = Provider::paginate($this->perpage);

			$pagination=(new Helper)->formatPagination($Providers);

			foreach($Providers as $index => $Provider){

				$Rides = UserRequests::where('provider_id',$Provider->id)
							->where('status','<>','CANCELLED')
							->get()->pluck('id');

				$Providers[$index]->rides_count = $Rides->count();

				$Providers[$index]->payment = UserRequestPayment::whereIn('request_id', $Rides)
								->select(\DB::raw(
								   'SUM(ROUND(provider_pay)) as overall, SUM(ROUND(provider_commission)) as commission' 
								))->get();
			}

			return view('admin.providers.provider-statement', compact('Providers','pagination'))->with('page','Providers Statement');

		} catch (Exception $e) {
			return back()->with('flash_error','Something Went Wrong!');
		}
	}

	public function statement_user(){

		try{

			$Users = User::paginate($this->perpage);

			$pagination=(new Helper)->formatPagination($Users);

			foreach($Users as $index => $User){

				$Rides = UserRequests::where('user_id',$User->id)
							->where('status','<>','CANCELLED')
							->get()->pluck('id');

				$Users[$index]->rides_count = $Rides->count();

				$Users[$index]->payment = UserRequestPayment::whereIn('request_id', $Rides)
								->select(\DB::raw(
								   'SUM(ROUND(total)) as overall' 
								))->get();
			}			

			return view('admin.providers.user-statement', compact('Users','pagination'))->with('page','Users Statement');

		} catch (Exception $e) {
			return back()->with('flash_error','Something Went Wrong!');
		}
	}

	public function statement_fleet(){

		try{

			$Fleets = Fleet::paginate($this->perpage);

			$pagination=(new Helper)->formatPagination($Fleets);

			foreach($Fleets as $index => $Fleet){

				$Rides = UserRequestPayment::where('fleet_id',$Fleet->id)->get()->pluck('id');

				$Fleets[$index]->rides_count = $Rides->count();

				$Fleets[$index]->payment = UserRequestPayment::where('fleet_id', $Fleet->id)
								->select(\DB::raw(
								   'SUM(ROUND(fleet)) as overall' 
								))->get();									
			}

			return view('admin.providers.fleet-statement', compact('Fleets','pagination'))->with('page','Fleets Statement');

		} catch (Exception $e) {
			return back()->with('flash_error','Something Went Wrong!');
		}
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function translation(){

		try{
			return view('admin.translation');
		}

		catch (Exception $e) {
			 return back()->with('flash_error','Something Went Wrong!');
		}
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function push(){

		try{
			$Pushes = CustomPush::orderBy('id','desc')->get();
			return view('admin.push',compact('Pushes'));
		}

		catch (Exception $e) {
			 return back()->with('flash_error','Something Went Wrong!');
		}
	}


	/**
	 * pages.
	 *
	 * @param  \App\Provider  $provider
	 * @return \Illuminate\Http\Response
	 */
	public function send_push(Request $request){


		$this->validate($request, [
				'send_to' => 'required|in:ALL,USERS,PROVIDERS',
				'user_condition' => ['required_if:send_to,USERS','in:ACTIVE,LOCATION,RIDES,AMOUNT'],
				'provider_condition' => ['required_if:send_to,PROVIDERS','in:ACTIVE,LOCATION,RIDES,AMOUNT'],
				'user_active' => ['required_if:user_condition,ACTIVE','in:HOUR,WEEK,MONTH'],
				'user_rides' => 'required_if:user_condition,RIDES',
				'user_location' => 'required_if:user_condition,LOCATION',
				'user_amount' => 'required_if:user_condition,AMOUNT',
				'provider_active' => ['required_if:provider_condition,ACTIVE','in:HOUR,WEEK,MONTH'],
				'provider_rides' => 'required_if:provider_condition,RIDES',
				'provider_location' => 'required_if:provider_condition,LOCATION',
				'provider_amount' => 'required_if:provider_condition,AMOUNT',
				'message' => 'required|max:100',
			]);

		try{

			$CustomPush = new CustomPush;
			$CustomPush->send_to = $request->send_to;
			$CustomPush->message = $request->message;

			if($request->send_to == 'USERS'){

				$CustomPush->condition = $request->user_condition;

				if($request->user_condition == 'ACTIVE'){
					$CustomPush->condition_data = $request->user_active;
				}elseif($request->user_condition == 'LOCATION'){
					$CustomPush->condition_data = $request->user_location;
				}elseif($request->user_condition == 'RIDES'){
					$CustomPush->condition_data = $request->user_rides;
				}elseif($request->user_condition == 'AMOUNT'){
					$CustomPush->condition_data = $request->user_amount;
				}

			}elseif($request->send_to == 'PROVIDERS'){

				$CustomPush->condition = $request->provider_condition;

				if($request->provider_condition == 'ACTIVE'){
					$CustomPush->condition_data = $request->provider_active;
				}elseif($request->provider_condition == 'LOCATION'){
					$CustomPush->condition_data = $request->provider_location;
				}elseif($request->provider_condition == 'RIDES'){
					$CustomPush->condition_data = $request->provider_rides;
				}elseif($request->provider_condition == 'AMOUNT'){
					$CustomPush->condition_data = $request->provider_amount;
				}
			}

			if($request->has('schedule_date') && $request->has('schedule_time')){
				$CustomPush->schedule_at = date("Y-m-d H:i:s",strtotime("$request->schedule_date $request->schedule_time"));
			}

			$CustomPush->save();

			if($CustomPush->schedule_at == ''){
				$this->SendCustomPush($CustomPush->id);
			}

			return back()->with('flash_success', 'Message Sent to all '.$request->segment);
		}

		catch (Exception $e) {
			 return back()->with('flash_error','Something Went Wrong!');
		}
	}


	public function SendCustomPush($CustomPush){

		try{

			\Log::notice("Starting Custom Push");

			$Push = CustomPush::findOrFail($CustomPush);

			if($Push->send_to == 'USERS'){

				$Users = [];

				if($Push->condition == 'ACTIVE'){

					if($Push->condition_data == 'HOUR'){

						$Users = User::whereHas('trips', function($query) {
							$query->where('created_at','>=',Carbon::now()->subHour());
						})->get();
						
					}elseif($Push->condition_data == 'WEEK'){

						$Users = User::whereHas('trips', function($query){
							$query->where('created_at','>=',Carbon::now()->subWeek());
						})->get();

					}elseif($Push->condition_data == 'MONTH'){

						$Users = User::whereHas('trips', function($query){
							$query->where('created_at','>=',Carbon::now()->subMonth());
						})->get();

					}

				}elseif($Push->condition == 'RIDES'){

					$Users = User::whereHas('trips', function($query) use ($Push){
								$query->where('status','COMPLETED');
								$query->groupBy('id');
								$query->havingRaw('COUNT(*) >= '.$Push->condition_data);
							})->get();


				}elseif($Push->condition == 'LOCATION'){

					$Location = explode(',', $Push->condition_data);

					$distance = config('constants.provider_search_radius', '10');
					$latitude = $Location[0];
					$longitude = $Location[1];

					$Users = User::whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
							->get();

				}


				foreach ($Users as $key => $user) {
					(new SendPushNotification)->sendPushToUser($user->id, $Push->message);
				}

			}elseif($Push->send_to == 'PROVIDERS'){


				$Providers = [];

				if($Push->condition == 'ACTIVE'){

					if($Push->condition_data == 'HOUR'){

						$Providers = Provider::whereHas('trips', function($query){
							$query->where('created_at','>=',Carbon::now()->subHour());
						})->get();
						
					}elseif($Push->condition_data == 'WEEK'){

						$Providers = Provider::whereHas('trips', function($query){
							$query->where('created_at','>=',Carbon::now()->subWeek());
						})->get();

					}elseif($Push->condition_data == 'MONTH'){

						$Providers = Provider::whereHas('trips', function($query){
							$query->where('created_at','>=',Carbon::now()->subMonth());
						})->get();

					}

				}elseif($Push->condition == 'RIDES'){

					$Providers = Provider::whereHas('trips', function($query) use ($Push){
							   $query->where('status','COMPLETED');
								$query->groupBy('id');
								$query->havingRaw('COUNT(*) >= '.$Push->condition_data);
							})->get();

				}elseif($Push->condition == 'LOCATION'){

					$Location = explode(',', $Push->condition_data);

					$distance = config('constants.provider_search_radius', '10');
					$latitude = $Location[0];
					$longitude = $Location[1];

					$Providers = Provider::whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
							->get();

				}


				foreach ($Providers as $key => $provider) {
					(new SendPushNotification)->sendPushToProvider($provider->id, $Push->message);
				}

			}elseif($Push->send_to == 'ALL'){

				$Users = User::all();
				foreach ($Users as $key => $user) {
					(new SendPushNotification)->sendPushToUser($user->id, $Push->message);
				}

				$Providers = Provider::all();
				foreach ($Providers as $key => $provider) {
					(new SendPushNotification)->sendPushToProvider($provider->id, $Push->message);
				}

			}
		}

		catch (Exception $e) {
			 return back()->with('flash_error','Something Went Wrong!');
		}
	}


	public function transactions(){

		try{
			$wallet_transation = AdminWallet::orderBy('id','desc')
								->paginate(config('constants.per_page', '10'));
			
			$pagination=(new Helper)->formatPagination($wallet_transation);   
			
			$wallet_balance=AdminWallet::sum('amount');

			return view('admin.wallet.wallet_transation',compact('wallet_transation','pagination','wallet_balance'));
			
		}

		catch (Exception $e) {
			 return back()->with('flash_error',$e->getMessage());
		}
	}

	public function transferlist(Request $request){

		$croute= Route::currentRouteName();
		
		if($croute=='admin.fleettransfer')
			$type='fleet';
		else
			$type='provider';

		$pendinglist = WalletRequests::where('request_from',$type)->where('status',0);
		if($croute=='admin.fleettransfer')
			$pendinglist = $pendinglist->with('fleet');
		else
			$pendinglist = $pendinglist->with('provider');

		$pendinglist = $pendinglist->get();
			   
		return view('admin.wallet.transfer',compact('pendinglist','type'));
	}

	public function approve(Request $request, $id){

		if($request->send_by == "online") {
			$response=(new PaymentController)->send_money($request, $id);
		}
		else{
			(new TripController)->settlements($id);
			$response['success']='Amount successfully send';
		}    

		if(!empty($response['error']))
			$result['flash_error']=$response['error'];
		if(!empty($response['success']))
			$result['flash_success']=$response['success'];
	   
		return redirect()->back()->with($result);
		
	}

	public function requestcancel(Request $request)
	{
		
		$cancel=(new TripController())->requestcancel($request);
		$response=json_decode($cancel->getContent(),true);
		
		if(!empty($response['error']))
			$result['flash_error']=$response['error'];
		if(!empty($response['success']))
			$result['flash_success']=$response['success'];

		return redirect()->back()->with($result);
	}

	public function transfercreate(Request $request, $id){
		$type=$id;
		return view('admin.wallet.create',compact('type'));        
	}

	public function search(Request $request){

		$results=array();

		$term =  $request->input('stext');       
		$sflag =  $request->input('sflag');

		if($sflag==1)
			$queries = Provider::where('first_name', 'LIKE', $term.'%')->take(5)->get();
		else
			$queries = Fleet::where('name', 'LIKE', $term.'%')->take(5)->get();

		foreach ($queries as $query)
		{
			$results[]=$query;
		}    

		return response()->json(array('success' => true, 'data'=>$results));

	}

	public function search_user(Request $request){

		$results=array();

		$term =  $request->input('stext');  

		$queries = User::where('first_name', 'LIKE', $term.'%')->take(5)->get();

		foreach ($queries as $query)
		{
			$results[]=$query;
		}    

		return response()->json(array('success' => true, 'data'=>$results));

	}

	public function search_provider(Request $request){

		$results=array();

		$term =  $request->input('stext');  

		$queries = Provider::where('first_name', 'LIKE', $term.'%')->take(5)->get();

		foreach ($queries as $query)
		{
			$results[]=$query;
		}    

		return response()->json(array('success' => true, 'data'=>$results));

	}

	public function search_ride(Request $request){

		$results=array();

		$term =  $request->input('stext');

		if($request->input('sflag')==1){
			
			$queries = UserRequests::where('provider_id', $request->id)->orderby('id', 'desc')->take(10)->get();
		}
		else{

			$queries = UserRequests::where('user_id', $request->id)->orderby('id', 'desc')->take(10)->get();
		}

		foreach ($queries as $query)
		{
			$results[]=$query;
		}

		return response()->json(array('success' => true, 'data'=>$results));

	}

	public function transferstore(Request $request){

		try{
			if($request->stype==1)
				$type='provider';
			else
				$type='fleet';

			$nextid=Helper::generate_request_id($type); 

			$amountRequest=new WalletRequests;
			$amountRequest->alias_id=$nextid;
			$amountRequest->request_from=$type;          
			$amountRequest->from_id=$request->from_id;
			$amountRequest->type=$request->type;
			$amountRequest->send_by=$request->by;
			$amountRequest->amount=$request->amount;

			$amountRequest->save();

			if($type == 'provider' && $request->type == 'C') {
				$provider = Provider::find($request->from_id);
				if($provider->status == 'balance' && (($provider->wallet_balance + $request->amount) > config('constants.minimum_negative_balance')) ) {
					ProviderService::where('provider_id', $request->from_id)->update(['status' => 'active']);
					Provider::where('id', $request->from_id)->update(['status' => 'approved']);
				}
			}
			

			//create the settlement transactions
			(new TripController)->settlements($amountRequest->id);            

			return back()->with('flash_success','Settlement processed successfully');
			
		}

		catch (Exception $e) {
			 return back()->with('flash_error',$e->getMessage());
		}      
	}

	public function download(Request $request, $id)
	{

		$documents = ProviderDocument::where('provider_id', $id)->get();

		if(!empty($documents->toArray())){

		   
			$Provider = Provider::findOrFail($id);

			// Define Dir Folder
			$public_dir=public_path();

			// Zip File Name
			$zipFileName = $Provider->first_name.'.zip';

			// Create ZipArchive Obj
			$zip = new ZipArchive;

			if ($zip->open($public_dir . '/storage/' . $zipFileName, ZipArchive::CREATE) === TRUE) {
				// Add File in ZipArchive
				foreach($documents as $file){
					$zip->addFile($public_dir.'/storage/'.$file->url);
				}
				
				// Close ZipArchive     
				$zip->close();
			}
			// Set Header
			$headers = array(
				'Content-Type' => 'application/octet-stream',
			);

			$filetopath=$public_dir.'/storage/'.$zipFileName;
			
			// Create Download Response
			if(file_exists($filetopath)){
				return response()->download($filetopath,$zipFileName,$headers)->deleteFileAfterSend(true);
			}            

			return redirect()
				->route('admin.provider.document.index', $id)
				->with('flash_success', 'documents downloaded successfully.');   
		}
		
		return redirect()
				->route('admin.provider.document.index', $id)
				->with('flash_error', 'failed to downloaded documents.');      
		
	}

	 /* DataBase BackUp*/
	 public function DBbackUp()
	 {
		if(config('constants.demo_mode', 0) == 1){
		 $host           = env('DB_HOST','');
		 $username       = env('DB_USERNAME','');
		 $password       = env('DB_PASSWORD','');
		 $database       = env('DB_DATABASE','');
		 $dateFormat     = $database."_".(new \DateTime())->format('Y-m-d');
		 $fileName       = public_path('/').$dateFormat.".sql";
		 if(!empty($password))
			 $command        = sprintf('mysqldump -h %s -u %s -p\'%s\' %s > %s', $host, $username,$password, $database, $fileName);
			 else
			 $command        = sprintf('mysqldump -h %s -u %s %s > %s', $host, $username, $database, $fileName);
			 exec($command);

		 return response()->download($fileName)->deleteFileAfterSend(true);
		}else{
			return back()->with('flash_error','Permission Denied.');	
		}
	 }


    public function save_subscription($id, Request $request) {

        $user = \App\Provider::findOrFail($id);

        $user->updatePushSubscription($request->input('endpoint'), $request->input('keys.p256dh'), $request->input('keys.auth'), 'web');

        return response()->json([ 'success' => true ]);
    }

	 public function fare(Request $request){

	 	$this->validate($request,[
				's_latitude' => 'required|numeric',
				's_longitude' => 'numeric',
				'd_latitude' => 'required|numeric',
				'd_longitude' => 'numeric',
				'service_type' => 'required|numeric|exists:service_types,id',
			]);

		try{       
			$response = new ServiceTypes();
			$responsedata=$response->calculateFare($request->all(),1);

			if(!empty($responsedata['errors'])){
				throw new Exception($responsedata['errors']);
			}
			else{
				return response()->json($responsedata['data']);
			}

		} catch(Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	 }

	 public function qrcode(Request $request){

		try{

			$Users = User::all();

			foreach ($Users as $key => $user) {

				$file=QrCode::format('png')->generate('{
	                "country_code":'.'"'.$user['country_code'].'"'.',
	                "phone_number":'.'"'.$user['mobile'].'"'.'
                }');
           
            	$fileName = Helper::upload_qrCode($user['mobile'],$file);

				User::where('id', $user->id)->update(['qrcode_url' => $fileName]);

			}

			return back()->with('flash_success','QR Code updated successfully');
			
		} catch(Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	 }
}
