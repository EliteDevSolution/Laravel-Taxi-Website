<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use DB;
use Log;
use Auth;
use Hash;
use Route;
use Storage;
use Setting;
use Exception;
use Validator;
use Notification;
use QrCode;

use Carbon\Carbon;
use App\Notifications\WebPush;
use App\Http\Controllers\SendPushNotification;
use App\Notifications\ResetPasswordOTP;
use App\Helpers\Helper;

use App\Card;
use App\User;
use App\Work;
use App\Admin;
use App\Reason;
use App\Provider;
use App\Settings;
use App\Promocode;
use App\UserWallet;
use App\ServiceType;
use App\UserRequests;
use App\RequestFilter;
use App\PromocodeUsage;
use App\WalletPassbook;
use App\ProviderService;
use Location\Coordinate;
use App\UserRequestRating;
use App\PromocodePassbook;
use App\UserRequestDispute;
use App\UserRequestLostItem;
use Location\Distance\Vincenty;
use App\Http\Controllers\ProviderResources\TripController;
use App\Http\Controllers\Resource\ReferralResource;

use App\Services\ServiceTypes;




class UserApiController extends Controller
{	

	public function __construct()
    {
        //Session::flush();
        //$this->middleware('admin.guest', ['except' => 'logout']);
        //parent::__construct();
    }
	/**  Check Email/Mobile Availablity Of a User  **/

	public function verify(Request $request)
	{
		// $this->validate($request, [
		// 		'email' => 'required|email|unique:users',
				
		// 	]);
			if($request->email == '') {
				return response()->json(['message' =>'Please enter email address'], 422);  
			}

		    $email_case = User::where('email', $request->email)->first();
			//User Already Exists
			if($email_case) {
				return response()->json(['message' =>'Email already exist. Enter new email'], 422);  
			}

		try{
			
			return response()->json(['message' => trans('api.email_available')]);

		} catch (Exception $e) {
			 return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
	}

	public function checkUserEmail(Request $request)
	{
		$this->validate($request, [
				'email' => 'required|email',                
			]);

		try{
			
			$email=$request->email;

			$results=User::where('email',$email)->first();

			if(empty($results))
				return response()->json(['message' => trans('api.email_available'),'is_available' => true]);                
			else        
				return response()->json(['message' => trans('api.email_not_available'),'is_available' => false]);

		} catch (Exception $e) {
			 return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
	}

	public function login(Request $request)
	{

		$tokenRequest = $request->create('/oauth/token', 'POST', $request->all());
		$request->request->add([
		   "client_id"     => $request->client_id,
		   "client_secret" => $request->client_secret,
		   "grant_type"    => 'password',
		   "code"          => '*',
		]);
		$response = Route::dispatch($tokenRequest);

		$json = (array) json_decode($response->getContent());

		if(!empty($json['error'])){
			$json['error']=$json['message'];
		}

		if(empty($json['error'])){
			if(Auth::guard("web")->attempt(['email'=>$request->username, 'password' => $request->password])) {
				$user = Auth::guard("web")->user();
				if($user) {
					$accessTokens = DB::table('oauth_access_tokens')->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
					$t=1;
					foreach ($accessTokens as $accessToken) {
						if($t!=1){                           
							DB::table('oauth_refresh_tokens') ->where('access_token_id', $accessToken->id)->delete();
							DB::table('oauth_access_tokens')->where('id', $accessToken->id)->delete();
						}                   
						$t++;
					}
				}
			}
		}    

		// $json['status'] = true;
		$response->setContent(json_encode($json));

		$update = User::where('email', $request->username)->update(['device_token' => $request->device_token , 'device_id' => $request->device_id , 'device_type' => $request->device_type]);    

		return $response;
	}

	public function signup(Request $request)
	{
		if($request->referral_code != null){
			$validate['referral_unique_id']=$request->referral_code;
			$validator  = (new ReferralResource)->checkReferralCode($validate);        
			if (!$validator->fails()) { 
				$validator->errors()->add('referral_code', 'Invalid Referral Code');
				throw new \Illuminate\Validation\ValidationException($validator);
			}   
		}

		$referral_unique_id=(new ReferralResource)->generateCode();

		$this->validate($request, [
				'social_unique_id' => ['required_if:login_by,facebook,google','unique:users'],
				'device_type' => 'required|in:android,ios',
				'device_token' => 'required',
				'device_id' => 'required',
				'login_by' => 'required|in:manual,facebook,google',
				'first_name' => 'required|max:255',
				'last_name' => 'required|max:255',
				'email' => 'required|email|max:255',
				'country_code' => 'required',
				'mobile' => 'required',
				'password' => 'required|min:6',
			]);

			$currentUser = null;

			$email_case = User::where('email', $request->email)->where([['country_code',$request->country_code],['mobile', $request->mobile]])->first();

			$registeredEmail = User::where('email', $request->email)->where('user_type', 'INSTANT')->first();
			$registeredMobile = User::where([['country_code',$request->country_code],['mobile', $request->mobile]])->where('user_type', 'INSTANT')->first();

			$registeredEmailNormal = User::where('email', $request->email)->where('user_type', 'NORMAL')->first();
        	$registeredMobileNormal = User::where([['country_code',$request->country_code],['mobile', $request->mobile]])->where('user_type', 'NORMAL')->first();

			//User Already Exists
			if($email_case != null) {
				return response()->json(['message' =>'User already registered!'], 422);  
			}

			if($registeredEmail != null && $registeredMobile != null) {
				//User Already Registerd with same credentials
				if($registeredEmail != null) return response()->json(['message' =>'User already registered with given email-Id!'], 422);
				else if($registeredMobile != null) return response()->json(['message' =>'User already registered with given mobile number!'], 422);

			} else {
				if($registeredEmail != null) $currentUser = $registeredEmail;
				else if($registeredMobile != null) $currentUser = $registeredMobile;
			}

			if($registeredEmailNormal != null) return response()->json(['message' =>'User already registered with given email-Id!'], 422);
            else if($registeredMobileNormal != null) return response()->json(['message' =>'User already registered with given mobile number!'], 422);
			// QrCode generator
			$file=QrCode::format('png')->size(500)->margin(10)->generate('{
                "country_code":'.'"'.$request->country_code.'"'.',
                "phone_number":'.'"'.$request->mobile.'"'.'
                }');
            // $file=QrCode::format('png')->size(200)->margin(20)->phoneNumber($request->country_code.$request->mobile);
            $fileName = Helper::upload_qrCode($request->mobile,$file);
			if($currentUser == null) {

				$User = $request->all();

				$User['payment_mode'] = 'CASH';
				$User['password'] = bcrypt($request->password);
				$User['referral_unique_id'] = $referral_unique_id;
				$User['qrcode_url'] = $fileName;
				$User = User::create($User);

				$User=Auth::loginUsingId($User->id);
				$UserToken = $User->createToken('AutoLogin');
				$User['access_token'] = $UserToken->accessToken;
				$User['currency'] = config('constants.currency');
				$User['sos'] = config('constants.sos_number', '911');                
				$User['app_contact'] = config('constants.app_contact', '5777');
				$User['measurement'] = config('constants.distance', 'Kms'); 

			} else {
				$User = $currentUser;
				$User->first_name = $request->first_name;
				$User->last_name = $request->last_name;
				$User->email = $request->email;
				$User->country_code = $request->country_code;
				$User->mobile = $request->mobile;
				$User->password = bcrypt($request->password);
				$User->login_by = 'manual';
				$User->payment_mode = 'CASH';
				$User->user_type = 'NORMAL';
				$User->referral_unique_id = $referral_unique_id;
				$User->qrcode_url = $fileName;
				$User->save();
			}
					   

			if(config('constants.send_email', 0) == 1) {
				// send welcome email here
				Helper::site_registermail($User);
			}  

			//check user referrals
			if(config('constants.referral', 0) == 1) {
				if($request->referral_code != null){
					//call referral function
					(new ReferralResource)->create_referral($request->referral_code,$User);                
				}
			}  

			return $User;
	   
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function logout(Request $request)
	{
		try {
			User::where('id', $request->id)->update(['device_id'=> '', 'device_token' => '']);
			return response()->json(['message' => trans('api.logout_success')]);
		} catch (Exception $e) {
			return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
	}


	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function change_password(Request $request){

		$this->validate($request, [
				'password' => 'required|confirmed|min:6',
				'old_password' => 'required',
			]);

		$User = Auth::user();

		if(Hash::check($request->old_password, $User->password))
		{
			$User->password = bcrypt($request->password);
			$User->save();

			if($request->ajax()) {
				return response()->json(['message' => trans('api.user.password_updated')]);
			}else{
				return back()->with('flash_success', trans('api.user.password_updated'));
			}

		} else {
			if($request->ajax()) {
				return response()->json(['error' => trans('api.user.incorrect_old_password')], 422);
			}else{
				return back()->with('flash_error',trans('api.user.incorrect_old_password'));
			}
		}

	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function update_location(Request $request){

		$this->validate($request, [
				'latitude' => 'required|numeric',
				'longitude' => 'required|numeric',
			]);

		if($user = User::find(Auth::user()->id)){

			$user->latitude = $request->latitude;
			$user->longitude = $request->longitude;
			$user->save();

			return response()->json(['message' => trans('api.user.location_updated')]);

		}else{

			return response()->json(['error' => trans('api.user.user_not_found')], 422);

		}

	}

	public function update_language(Request $request){

		$this->validate($request, [
				'language' => 'required',                
			]);

		if($user = User::find(Auth::user()->id)){

			$user->language = $request->language;           
			$user->save();

			return response()->json(['message' => trans('api.user.language_updated'),'language'=>$request->language]);

		}else{

			return response()->json(['error' => trans('api.user.user_not_found')], 422);

		}

	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function details(Request $request){

		$this->validate($request, [
			'device_type' => 'in:android,ios',
		]);

		try{

			if($user = User::find(Auth::user()->id)){

				if($request->has('device_token')){
					$user->device_token = $request->device_token;
				}

				if($request->has('device_type')){
					$user->device_type = $request->device_type;
				}

				if($request->has('device_id')){
					$user->device_id = $request->device_id;
				}

				$user->save();

				if($user->language != null) {
					app()->setLocale($user->language);
				}

				$align = ($user->language == 'ar') ? 'text-align: right' : '';

				$user->currency = config('constants.currency');
				$user->sos = config('constants.sos_number', '911');                
				$user->app_contact = config('constants.app_contact', '5777');                
				$user->measurement = config('constants.distance', 'Kms');


				$user->cash =(int)config('constants.cash');
	            $user->card =(int)config('constants.card');
	            $user->payumoney =(int)config('constants.payumoney');
	            $user->paypal =(int)config('constants.paypal');
	            $user->paypal_adaptive =(int)config('constants.paypal_adaptive');
	            $user->braintree =(int)config('constants.braintree');
	            $user->paytm =(int)config('constants.paytm');

				$user->stripe_secret_key = config('constants.stripe_secret_key');
				$user->stripe_publishable_key = config('constants.stripe_publishable_key');
            	$user->stripe_currency = config('constants.stripe_currency');

				$user->payumoney_environment = config('constants.payumoney_environment');
	            $user->payumoney_key = config('constants.payumoney_key');
	            $user->payumoney_salt = config('constants.payumoney_salt');
	            $user->payumoney_auth = config('constants.payumoney_auth');

	            $user->paypal_environment = config('constants.paypal_environment');
	            $user->paypal_currency = config('constants.paypal_currency');
	            $user->paypal_client_id = config('constants.paypal_client_id');
	            $user->paypal_client_secret = config('constants.paypal_client_secret');

	            $user->braintree_environment = config('constants.braintree_environment');
	            $user->braintree_merchant_id = config('constants.braintree_merchant_id');
	            $user->braintree_public_key = config('constants.braintree_public_key');
	            $user->braintree_private_key = config('constants.braintree_private_key');

				$user->referral_count = config('constants.referral_count', '0');
				$user->referral_amount = config('constants.referral_amount', '0');
				$user->referral_text = trans('api.user.invite_friends');
				$user->referral_total_count = (new ReferralResource)->get_referral(1, Auth::user()->id)[0]->total_count;
				$user->referral_total_amount = (new ReferralResource)->get_referral(1, Auth::user()->id)[0]->total_amount;
				$user->referral_total_text = "<p style='font-size:16px; color: #000; $align'>".trans('api.user.referral_amount').": ".$user->referral_total_amount."<br>".trans('api.user.referral_count').": ".$user->referral_total_count."</p>";
				$user->ride_otp = (int)config('constants.ride_otp');
				return $user;

			} else {
				return response()->json(['error' => trans('api.user.user_not_found')], 422);
			}
		}
		catch (Exception $e) {
			return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}

	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function update_profile(Request $request)
	{

		$this->validate($request, [
				'first_name' => 'required|max:255',
				'last_name' => 'max:255',
				'email' => 'email|unique:users,email,'.Auth::user()->id,
				'picture' => 'mimes:jpeg,bmp,png',
			]);

		 try {

			$user = User::findOrFail(Auth::user()->id);

			if($request->has('first_name')){ 
				$user->first_name = $request->first_name;
			}
			
			if($request->has('last_name')){
				$user->last_name = $request->last_name;
			}
			
			if($request->has('country_code')){
				$user->country_code = $request->country_code;
			}
			if($request->has('gender')){
				$user->gender = $request->gender;
			}
			if($request->has('mobile') && $request->mobile != null){
				
					$Provider = User::where([['country_code',$request->country_code],['mobile', $request->mobile]])->where('user_type', 'NORMAL')->where('id','!=',Auth::user()->id)->first();
					if($Provider != null) {
						return response()->json(['message' => trans('api.mobile_exist')], 422);
					} 
			
				$user->mobile = $request->mobile;
				// QrCode generator
				$file=QrCode::format('png')->size(500)->margin(10)->generate('{
					"country_code":'.'"'.$request->country_code.'"'.',
					"phone_number":'.'"'.$request->mobile.'"'.'
					}');
				// $file=QrCode::format('png')->size(200)->margin(20)->phoneNumber($request->country_code.$request->mobile);
				$fileName = Helper::upload_qrCode($request->mobile,$file);
				$user->qrcode_url = $fileName;
			}
			
			if($request->has('gender')){
				$user->gender = $request->gender;
			}

			if($request->has('language')){
				$user->language = $request->language;
			}

			if ($request->picture != "") {
				Storage::delete($user->picture);
				$user->picture = $request->picture->store('user/profile');
			}

			$user->save();

			$user->currency = config('constants.currency');
			$user->sos = config('constants.sos_number', '911');                
			$user->app_contact = config('constants.app_contact', '5777');
			$user->measurement = config('constants.distance', 'Kms');

			if($user->language != null) {
				app()->setLocale($user->language);
			}

			$align = ($user->language == 'ar') ? 'text-align: right' : '';


			$user->referral_count = config('constants.referral_count', '0');
			$user->referral_amount = config('constants.referral_amount', '0');
			$user->referral_text = trans('api.user.invite_friends');
			$user->referral_total_count = (new ReferralResource)->get_referral('user', Auth::user()->id)[0]->total_count;
			$user->referral_total_amount = (new ReferralResource)->get_referral('user', Auth::user()->id)[0]->total_amount;
			$user->referral_total_text = "<p style='font-size:16px; color: #000; $align'>".trans('api.user.referral_amount').": ".(new ReferralResource)->get_referral('user', Auth::user()->id)[0]->total_amount."<br>".trans('api.user.referral_count').": ".(new ReferralResource)->get_referral('user', Auth::user()->id)[0]->total_count."</p>";

			if($request->ajax()) {
				return response()->json($user);
			}else{
				return back()->with('flash_success', trans('api.user.profile_updated'));
			}
		}

		catch (ModelNotFoundException $e) {
			 return response()->json(['error' => trans('api.user.user_not_found')], 422);
		}

	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function services() {

		if($serviceList = ServiceType::all()) {
			return $serviceList;
		} else {
			return response()->json(['error' => trans('api.services_not_found')], 422);
		}

	}


	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function send_request(Request $request) {

		if(config('constants.card') == 1)
		{
			$this->validate($request, [
				's_latitude' => 'required|numeric',
				's_longitude' => 'numeric',
				'd_latitude' => 'numeric',
				'd_longitude' => 'numeric',
				'service_type' => 'required|numeric|exists:service_types,id',
				//'promo_code' => 'exists:promocodes,promo_code',
				'distance' => 'required|numeric',
				'use_wallet' => 'numeric',
				'payment_mode' => 'required|in:BRAINTREE,CASH,CARD,PAYPAL,PAYSTACK,PAYPAL-ADAPTIVE,PAYUMONEY,PAYTM',
				'card_id' => ['required_if:payment_mode,CARD','exists:cards,card_id,user_id,'.Auth::user()->id],
			]);

		} else if(config('constants.paystack') == 1)
		{
			$this->validate($request, [
				's_latitude' => 'required|numeric',
				's_longitude' => 'numeric',
				'd_latitude' => 'numeric',
				'd_longitude' => 'numeric',
				'service_type' => 'required|numeric|exists:service_types,id',
				//'promo_code' => 'exists:promocodes,promo_code',
				'distance' => 'required|numeric',
				'use_wallet' => 'numeric',
				'payment_mode' => 'required|in:BRAINTREE,CASH,CARD,PAYPAL,PAYSTACK,PAYPAL-ADAPTIVE,PAYUMONEY,PAYTM',
				'card_id' => ['required_if:payment_mode,CARD','exists:paystack_cards,card_id,user_id,'.Auth::user()->id],
			]);	
		}

		/*Log::info('New Request from User: '.Auth::user()->id);
		Log::info('Request Details:', $request->all());*/

		$ActiveRequests = UserRequests::PendingRequest(Auth::user()->id)->count();

		if($ActiveRequests > 0) {
			if($request->ajax()) {
				return response()->json(['error' => trans('api.ride.request_inprogress')], 422);
			} else {
				return redirect('dashboard')->with('flash_error', trans('api.ride.request_inprogress'));
			}
		}

		if($request->has('schedule_date') && $request->has('schedule_time')){

			if( (new Carbon("$request->schedule_date $request->schedule_time"))->diffInMinutes(Carbon::now()) < config('constants.schedule_time', '20')  ) {
				if($request->ajax()) {
					return response()->json(['error' => trans('api.ride.request_scheduled_time')], 422);
				}else{
					return redirect('dashboard')->with('flash_error', trans('api.ride.request_scheduled_time'));
				}
			}

			$beforeschedule_time = (new Carbon("$request->schedule_date $request->schedule_time"))->subHour(1);
			$afterschedule_time = (new Carbon("$request->schedule_date $request->schedule_time"))->addHour(1);

			$CheckScheduling = UserRequests::where('status','SCHEDULED')
							->where('user_id', Auth::user()->id)
							->whereBetween('schedule_at',[$beforeschedule_time,$afterschedule_time])
							->count();


			if($CheckScheduling > 0){
				if($request->ajax()) {
					return response()->json(['error' => trans('api.ride.request_scheduled')], 422);
				}else{
					return redirect('dashboard')->with('flash_error', trans('api.ride.request_scheduled'));
				}
			}

		}

		$distance = config('constants.provider_search_radius', '10');
		//$distance = 1000;
	   
		$latitude = $request->s_latitude;
		$longitude = $request->s_longitude;
		$service_type = $request->service_type;

		
		$Providers = Provider::with('service')
			->select(DB::Raw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) AS distance"),'id')
			->where('status', 'approved')
			->whereRaw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
			->whereHas('service', function($query) use ($service_type){
						$query->where('status','active');
						$query->where('service_type_id',$service_type);
					})
			->orderBy('distance','asc')
			->get();
		
		//  dd($Providers);
		// List Providers who are currently busy and add them to the filter list.

		if(count($Providers) == 0) {
			if($request->ajax()) {
				// Push Notification to User
				return response()->json(['error' => trans('api.ride.no_providers_found')], 422); 
			}else{
				return back()->with('flash_success', trans('api.ride.no_providers_found'));
			}
		}

		try{

			$details = "https://maps.googleapis.com/maps/api/directions/json?origin=".$request->s_latitude.",".$request->s_longitude."&destination=".$request->d_latitude.",".$request->d_longitude."&mode=driving&key=".config('constants.map_key');

			$json = curl($details);

			$details = json_decode($json, TRUE);

			$route_key = (count($details['routes']) > 0) ? $details['routes'][0]['overview_polyline']['points'] : '';

			$UserRequest = new UserRequests;
			$UserRequest->booking_id = Helper::generate_booking_id();
			if($request->has('braintree_nonce') && $request->braintree_nonce != null){
				$UserRequest->braintree_nonce = $request->braintree_nonce;
			}
		 

			$UserRequest->user_id = Auth::user()->id;
			
			if((config('constants.manual_request',0) == 0) && (config('constants.broadcast_request',0) == 0)){
				$UserRequest->current_provider_id = $Providers[0]->id;
			}else{
				$UserRequest->current_provider_id = 0;
			}

			$UserRequest->service_type_id = $request->service_type;
			$UserRequest->rental_hours = $request->rental_hours;
			$UserRequest->payment_mode = $request->payment_mode;
			$UserRequest->promocode_id = $request->promocode_id ? : 0;
			
			$UserRequest->status = 'SEARCHING';

			$UserRequest->s_address = $request->s_address ? : "";
			$UserRequest->d_address = $request->d_address ? : "";

			$UserRequest->s_latitude = $request->s_latitude;
			$UserRequest->s_longitude = $request->s_longitude;

			$UserRequest->d_latitude = $request->d_latitude ? $request->d_latitude : $request->s_latitude;
			$UserRequest->d_longitude = $request->d_longitude ? $request->d_longitude : $request->s_longitude;

			if($request->d_latitude == null && $request->d_longitude == null) {
				$UserRequest->is_drop_location = 0;
			}

			$UserRequest->destination_log = json_encode([['latitude' => $UserRequest->d_latitude, 'longitude' => $request->d_longitude, 'address' => $request->d_address]]);
			$UserRequest->distance = $request->distance;
			$UserRequest->unit = config('constants.distance', 'Kms');

			if(Auth::user()->wallet_balance > 0){
				$UserRequest->use_wallet = $request->use_wallet ? : 0;
			}

			if(config('constants.track_distance', 0) == 1){
				$UserRequest->is_track = "YES";
			}

			$UserRequest->otp = mt_rand(1000 , 9999);

			$UserRequest->assigned_at = Carbon::now();
			$UserRequest->route_key = $route_key;

			if($Providers->count() <= config('constants.surge_trigger') && $Providers->count() > 0){
				$UserRequest->surge = 1;
			}

			if($request->has('schedule_date') && $request->has('schedule_time')){
				$UserRequest->status = 'SCHEDULED';
				$UserRequest->schedule_at = date("Y-m-d H:i:s",strtotime("$request->schedule_date $request->schedule_time"));
				$UserRequest->is_scheduled = 'YES';
			}

			if($UserRequest->status != 'SCHEDULED') {
				if((config('constants.manual_request',0) == 0) && (config('constants.broadcast_request',0) == 0)){
					//Log::info('New Request id : '. $UserRequest->id .' Assigned to provider : '. $UserRequest->current_provider_id);
					(new SendPushNotification)->IncomingRequest($Providers[0]->id);
				}
			}	

			$UserRequest->save();

			if((config('constants.manual_request',0) == 1)) {

				$admins = Admin::select('id')->get();

				foreach ($admins as $admin_id) {
					$admin = Admin::find($admin_id->id);
					$admin->notify(new WebPush("Notifications", trans('api.push.incoming_request'), route('admin.dispatcher.index') ));
				}

			}
		   

			// update payment mode
			User::where('id',Auth::user()->id)->update(['payment_mode' => $request->payment_mode]);

			if($request->has('card_id')){

				Card::where('user_id',Auth::user()->id)->update(['is_default' => 0]);
				Card::where('card_id',$request->card_id)->update(['is_default' => 1]);
			}

			if($UserRequest->status != 'SCHEDULED') {
				if(config('constants.manual_request',0) == 0){
					foreach ($Providers as $key => $Provider) {

						if(config('constants.broadcast_request',0) == 1){
						   (new SendPushNotification)->IncomingRequest($Provider->id); 
						}

						$Filter = new RequestFilter;
						// Send push notifications to the first provider
						// incoming request push to provider
						
						$Filter->request_id = $UserRequest->id;
						$Filter->provider_id = $Provider->id; 
						$Filter->save();
					}
				}
			}
			

			if($request->ajax()) {
				return response()->json([
						'message' => ($UserRequest->status == 'SCHEDULED') ? 'Schedule request created!' : 'New request created!',
						'request_id' => $UserRequest->id,
						'current_provider' => $UserRequest->current_provider_id,
					]);
			}else{
				if($UserRequest->status == 'SCHEDULED') {
					$request->session()->flash('flash_success', 'Your ride is scheduled!');
				}
				return redirect('dashboard');
			}

		} catch (Exception $e) {            
			if($request->ajax()) {
				return response()->json(['error' => trans('api.something_went_wrong')], 500);
			}else{
				return back()->with('flash_error', trans('api.something_went_wrong'));
			}
		}
	}


	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function cancel_request(Request $request) {
		
		$this->validate($request, [
			'request_id' => 'required|numeric|exists:user_requests,id,user_id,'.Auth::user()->id,
		]);

		try{

			$UserRequest = UserRequests::findOrFail($request->request_id);

			if($UserRequest->status == 'CANCELLED')
			{
				if($request->ajax()) {
					return response()->json(['error' => trans('api.ride.already_cancelled')], 422); 
				}else{
					return back()->with('flash_error', trans('api.ride.already_cancelled'));
				}
			}

			if(in_array($UserRequest->status, ['SEARCHING','STARTED','ARRIVED','SCHEDULED'])) {

				if($UserRequest->status != 'SEARCHING'){
					$this->validate($request, [
						'cancel_reason'=> 'max:255',
					]);
				}

				$UserRequest->status = 'CANCELLED';

				if($request->cancel_reason=='ot')
					$UserRequest->cancel_reason = $request->cancel_reason_opt;
				else
					$UserRequest->cancel_reason = $request->cancel_reason;

				$UserRequest->cancelled_by = 'USER';
				$UserRequest->save();

				RequestFilter::where('request_id', $UserRequest->id)->delete();

				if($UserRequest->status != 'SCHEDULED'){

					if($UserRequest->provider_id != 0){
						ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' => 'active']);
					}
				}

				 // Send Push Notification to User
				(new SendPushNotification)->UserCancellRide($UserRequest);

				if($request->ajax()) {
					return response()->json(['message' => trans('api.ride.ride_cancelled')]); 
				}else{
					return redirect('dashboard')->with('flash_success',trans('api.ride.ride_cancelled'));
				}

			} else {
				if($request->ajax()) {
					return response()->json(['error' => trans('api.ride.already_onride')], 422); 
				}else{
					return back()->with('flash_error', trans('api.ride.already_onride'));
				}
			}
		}

		catch (ModelNotFoundException $e) {
			if($request->ajax()) {
				return response()->json(['error' => trans('api.something_went_wrong')],500);
			}else{
				return back()->with('flash_error', trans('api.something_went_wrong'));
			}
		}

	}


	public function extend_trip(Request $request) {
		$this->validate($request, [
			'request_id' => 'required|numeric|exists:user_requests,id,user_id,'.Auth::user()->id,
			'latitude' => 'required|numeric',
			'longitude' => 'required|numeric',
			'address' => 'required',
		]);

		try{

			$UserRequest = UserRequests::findOrFail($request->request_id);

			$details = "https://maps.googleapis.com/maps/api/directions/json?origin=".$UserRequest->s_latitude.",".$UserRequest->s_longitude."&destination=".$request->latitude.",".$request->longitude."&mode=driving&key=".config('constants.map_key');

			$json = curl($details);

			$details = json_decode($json, TRUE);

			$route_key = (count($details['routes']) > 0) ? $details['routes'][0]['overview_polyline']['points'] : '';

			$destination_log = json_decode($UserRequest->destination_log);
			$destination_log[] = ['latitude' => $request->latitude, 'longitude' => $request->longitude, 'address' => $request->address];

			$UserRequest->d_latitude = $request->latitude;
			$UserRequest->d_longitude = $request->longitude;
			$UserRequest->d_address = $request->address;
			$UserRequest->route_key = $route_key;
			$UserRequest->destination_log = json_encode($destination_log);

			$UserRequest->save();

			$message = trans('api.destination_changed');

			(new SendPushNotification)->sendPushToProvider($UserRequest->provider_id, $message);

			(new SendPushNotification)->sendPushToUser($UserRequest->user_id, $message); 

			return $UserRequest;

		} catch (ModelNotFoundException $e) {
			if($request->ajax()) {
				return response()->json(['error' => trans('api.something_went_wrong')],500);
			}else{
				return back()->with('flash_error', trans('api.something_went_wrong'));
			}
		}
	}

	/**
	 * Show the request status check.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function request_status_check() {

		try{
			$check_status = ['CANCELLED', 'SCHEDULED'];

			$UserRequests = UserRequests::UserRequestStatusCheck(Auth::user()->id, $check_status)
										->get()
										->toArray();
										

			$search_status = ['SEARCHING','SCHEDULED'];
			$UserRequestsFilter = UserRequests::UserRequestAssignProvider(Auth::user()->id,$search_status)->get(); 

			 //Log::info($UserRequestsFilter);
			if(!empty($UserRequests)){
			    $UserRequests[0]['ride_otp'] = (int)config('constants.ride_otp', 0);

				$UserRequests[0]['reasons']=Reason::where('type','USER')->get();
			}

			$Timeout = config('constants.provider_select_timeout', 180);
			$type = config('constants.broadcast_request', 0);

			if(!empty($UserRequestsFilter)){
				for ($i=0; $i < sizeof($UserRequestsFilter); $i++) {
					if($type==1){
						$ExpiredTime = $Timeout - (time() - strtotime($UserRequestsFilter[$i]->created_at));
						if($UserRequestsFilter[$i]->status == 'SEARCHING' && $ExpiredTime < 0) {
							UserRequests::where('id', $UserRequestsFilter[$i]->id)->update(['status' => 'CANCELLED']);
							// No longer need request specific rows from RequestMeta
							RequestFilter::where('request_id', $UserRequestsFilter[$i]->id)->delete();
						}else if($UserRequestsFilter[$i]->status == 'SEARCHING' && $ExpiredTime > 0){
							break;
						}
					}
					else{
						$ExpiredTime = $Timeout - (time() - strtotime($UserRequestsFilter[$i]->assigned_at));
						if($UserRequestsFilter[$i]->status == 'SEARCHING' && $ExpiredTime < 0) {
							$Providertrip = new TripController();
							$Providertrip->assign_next_provider($UserRequestsFilter[$i]->id);
						}else if($UserRequestsFilter[$i]->status == 'SEARCHING' && $ExpiredTime > 0){
							break;
						}
					}	
					
				}

			}

			if(empty($UserRequests)) {

				$cancelled_request = UserRequests::where('user_requests.user_id', Auth::user()->id)
					->where('user_requests.user_rated',0)
					->where('user_requests.status', ['CANCELLED'])->orderby('updated_at', 'desc')
					->where('updated_at','>=',\Carbon\Carbon::now()->subSeconds(5))
					->first();

				if($cancelled_request != null) {
					\Session::flash('flash_error', $cancelled_request->cancel_reason);
				}
				
			}


		  
			return response()->json(['data' => $UserRequests , 
				'sos' => config('constants.sos_number', '911'), 
				'cash' => (int)config('constants.cash'), 
				'card' => (int)config('constants.card'),
				'currency'=>config('constants.currency','$'), 
				'payumoney' => (int)config('constants.payumoney'), 
				'paypal' => (int)config('constants.paypal'), 
				'paypal_adaptive' => (int)config('constants.paypal_adaptive'), 
				'braintree' => (int)config('constants.braintree'),  
				'paytm' => (int)config('constants.paytm'), 
				'stripe_secret_key' => config('constants.stripe_secret_key'), 
				'stripe_publishable_key' => config('constants.stripe_publishable_key'), 
				'stripe_currency' => config('constants.stripe_currency'), 
				'payumoney_environment' => config('constants.payumoney_environment'), 
				'payumoney_key' => config('constants.payumoney_key'), 
				'payumoney_salt' => config('constants.payumoney_salt'), 
				'payumoney_auth' => config('constants.payumoney_auth'), 
				'paypal_environment' => config('constants.paypal_environment'), 
				'paypal_currency' => config('constants.paypal_currency'), 
				'paypal_client_id' => config('constants.paypal_client_id'), 
				'paypal_client_secret' => config('constants.paypal_client_secret'), 
				'braintree_environment' => config('constants.braintree_environment'), 
				'braintree_merchant_id' => config('constants.braintree_merchant_id'), 
				'braintree_public_key' => config('constants.braintree_public_key'), 
				'braintree_private_key' => config('constants.braintree_private_key')]);

		} catch (Exception $e) {
			return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */


	public function rate_provider(Request $request) {

		$this->validate($request, [
				'request_id' => 'required|integer|exists:user_requests,id,user_id,'.Auth::user()->id,
				'rating' => 'required|integer|in:1,2,3,4,5',
				'comment' => 'max:255',
			]);
	
		$UserRequests = UserRequests::where('id' ,$request->request_id)
				->where('status' ,'COMPLETED')
				->where('paid', 0)
				->first();

		if ($UserRequests) {
			if($request->ajax()){
				return response()->json(['error' => trans('api.user.not_paid')], 422);
			} else {
				return back()->with('flash_error', trans('api.user.not_paid'));
			}
		}

		try{

			$UserRequest = UserRequests::findOrFail($request->request_id);
			
			if($UserRequest->rating == null) {
				UserRequestRating::create([
						'provider_id' => $UserRequest->provider_id,
						'user_id' => $UserRequest->user_id,
						'request_id' => $UserRequest->id,
						'user_rating' => $request->rating,
						'user_comment' => $request->comment,
					]);
			} else {
				$UserRequest->rating->update([
						'user_rating' => $request->rating,
						'user_comment' => $request->comment,
					]);
			}

			$UserRequest->user_rated = 1;
			$UserRequest->save();

			$average = UserRequestRating::where('provider_id', $UserRequest->provider_id)->avg('user_rating');

			Provider::where('id',$UserRequest->provider_id)->update(['rating' => $average]);

			// Send Push Notification to Provider 
			if($request->ajax()){
				return response()->json(['message' => trans('api.ride.provider_rated')]); 
			}else{
				return redirect('dashboard')->with('flash_success', trans('api.ride.provider_rated'));
			}
		} catch (Exception $e) {
			if($request->ajax()){
				return response()->json(['error' => trans('api.something_went_wrong')], 500);
			}else{
				return back()->with('flash_error', trans('api.something_went_wrong'));
			}
		}

	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */


	public function modifiy_request(Request $request) {

		$this->validate($request, [
				'request_id' => 'required|integer|exists:user_requests,id,user_id,'.Auth::user()->id,
				'latitude' => 'sometimes|nullable|numeric',
				'longitude' => 'sometimes|nullable|numeric',
				'address' => 'sometimes|nullable',
				'payment_mode' => 'sometimes|nullable|in:BRAINTREE,CASH,CARD,PAYPAL,PAYPAL-ADAPTIVE,PAYUMONEY,PAYTM',
				'card_id' => ['required_if:payment_mode,CARD','exists:cards,card_id,user_id,'.Auth::user()->id],
			]);

		try{

			$UserRequest = UserRequests::findOrFail($request->request_id);

			if(!empty($request->latitude) && !empty($request->longitude)){
				$UserRequest->d_latitude = $request->latitude?:$UserRequest->d_latitude;
				$UserRequest->d_longitude = $request->longitude?:$UserRequest->d_longitude;
				$UserRequest->d_address =  $request->address?:$UserRequest->d_address;
			}

			if($request->has('braintree_nonce') && $request->braintree_nonce != null){
				$UserRequest->braintree_nonce = $request->braintree_nonce;
			}

			if(!empty($request->payment_mode)){
				$UserRequest->payment_mode = $request->payment_mode?:$UserRequest->payment_mode;
				if($request->payment_mode=='CARD' && $UserRequest->status=='DROPPED'){
					$UserRequest->status='COMPLETED';
				}
			}
				
			$UserRequest->save();

			

			if($request->has('card_id')){

				Card::where('user_id',Auth::user()->id)->update(['is_default' => 0]);
				Card::where('card_id',$request->card_id)->update(['is_default' => 1]);
			}

			// Send Push Notification to Provider 
			if($request->ajax()){
				return response()->json(['message' => trans('api.ride.request_modify_location')]); 
			}else{
				return redirect('dashboard')->with('flash_success', trans('api.ride.request_modify_location'));
			}
		} catch (Exception $e) {
			if($request->ajax()){
				return response()->json(['error' => trans('api.something_went_wrong')], 500);
			}else{
				return back()->with('flash_error', trans('api.something_went_wrong'));
			}
		}

	} 


	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function trips() {
	
		try{
			$UserRequests = UserRequests::UserTrips(Auth::user()->id)->get();
			if(!empty($UserRequests)){
				$map_icon = asset('asset/img/marker-start.png');
				foreach ($UserRequests as $key => $value) {
					$UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
							"autoscale=1".
							"&size=320x130".
							"&maptype=terrian".
							"&format=png".
							"&visual_refresh=true".
							"&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
							"&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
							"&path=color:0x191919|weight:3|enc:".$value->route_key.
							"&key=".config('constants.map_key');
				}
			}
			return $UserRequests;
		}

		catch (Exception $e) {
			return response()->json(['error' => trans('api.something_went_wrong')]);
		}
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function estimated_fare(Request $request){

		$this->validate($request,[
				's_latitude' => 'required|numeric',
				's_longitude' => 'numeric',
				'd_latitude' => 'required|numeric',
				'd_longitude' => 'numeric',
				'service_type' => 'required|numeric|exists:service_types,id',
			]);

		try{       
			$response = new ServiceTypes();

			$responsedata=$response->calculateFare($request->all(), 1);

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

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function trip_details(Request $request) {

		 $this->validate($request, [
				'request_id' => 'required|integer|exists:user_requests,id',
			]);
	
		try{
			$UserRequests = UserRequests::UserTripDetails(Auth::user()->id,$request->request_id)->get();

			if(!empty($UserRequests)){
				$map_icon = asset('asset/img/marker-start.png');
				foreach ($UserRequests as $key => $value) {					
					$UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
							"autoscale=1".
							"&size=320x130".
							"&maptype=terrian".
							"&format=png".
							"&visual_refresh=true".
							"&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
							"&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
							"&path=color:0x191919|weight:3|enc:".$value->route_key.
							"&key=".config('constants.map_key');
				}

				
				$UserRequests[0]->dispute=UserRequestDispute::where('dispute_type','user')->where('request_id',$request->request_id)->where('user_id',Auth::user()->id)->first();

				$UserRequests[0]->lostitem=UserRequestLostItem::where('request_id',$request->request_id)->where('user_id',Auth::user()->id)->first();

				$UserRequests[0]->contact_number=config('constants.contact_number','');
				$UserRequests[0]->contact_email=config('constants.contact_email','');
				
			}
			return $UserRequests;
		}

		catch (Exception $e) {
			echo $e->getMessage();exit;
			return response()->json(['error' => trans('api.something_went_wrong')]);
		}
	}

	/**
	 * get all promo code.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function promocodes() {
		try{
			//$this->check_expiry();

			return PromocodeUsage::Active()
					->where('user_id', Auth::user()->id)
					->with('promocode')
					->get();

		} catch (Exception $e) {
			return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
	} 


	/*public function check_expiry(){
		try{
			$Promocode = Promocode::all();
			foreach ($Promocode as $index => $promo) {
				if(date("Y-m-d") > $promo->expiration){
					$promo->status = 'EXPIRED';
					$promo->save();
					PromocodeUsage::where('promocode_id', $promo->id)->update(['status' => 'EXPIRED']);
				}else{
					PromocodeUsage::where('promocode_id', $promo->id)
							->where('status','<>','USED')
							->update(['status' => 'ADDED']);

					PromocodePassbook::create([
							'user_id' => Auth::user()->id,
							'status' => 'ADDED',
							'promocode_id' => $promo->id
						]);
				}
			}
		} catch (Exception $e) {
			return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
	}*/


	/**
	 * add promo code.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function list_promocode(Request $request){
		try{

		$promo_list =Promocode::where('expiration','>=',date("Y-m-d H:i"))
				->whereDoesntHave('promousage', function($query) {
							$query->where('user_id',Auth::user()->id);
						})
				->get(); 
		if($request->ajax()){
			return response()->json([
					'promo_list' => $promo_list
				]);  
			 }else{
				return $promo_list;
			 }    
		} catch (Exception $e) {
			if($request->ajax()){
				return response()->json(['error' => trans('api.something_went_wrong')], 500);
			}else{
				return back()->with('flash_error', trans('api.something_went_wrong'));
			}
		}
	}
	

	public function add_promocode(Request $request) {

		$this->validate($request, [
				'promocode' => 'required|exists:promocodes,promo_code',
			]);

		try{

			$find_promo = Promocode::where('promo_code',$request->promocode)->first();

			if($find_promo->status == 'EXPIRED' || (date("Y-m-d") > $find_promo->expiration)){

				if($request->ajax()){

					return response()->json([
						'message' => trans('api.promocode_expired'), 
						'code' => 'promocode_expired'
					]);

				}else{
					return back()->with('flash_error', trans('api.promocode_expired'));
				}

			}elseif(PromocodeUsage::where('promocode_id',$find_promo->id)->where('user_id', Auth::user()->id)->whereIN('status',['ADDED','USED'])->count() > 0){

				if($request->ajax()){

					return response()->json([
						'message' => trans('api.promocode_already_in_use'), 
						'code' => 'promocode_already_in_use'
						]);

				}else{
					return back()->with('flash_error', trans('api.promocode_already_in_use'));
				}

			}else{

				$promo = new PromocodeUsage;
				$promo->promocode_id = $find_promo->id;
				$promo->user_id = Auth::user()->id;
				$promo->status = 'ADDED';
				$promo->save();
				
				$count_id = PromocodePassbook::where('promocode_id' , $find_promo->id)->count();
				//dd($count_id); 
				if($count_id == 0){

				   PromocodePassbook::create([
							'user_id' => Auth::user()->id,
							'status' => 'ADDED',
							'promocode_id' => $find_promo->id
						]);
				}
				if($request->ajax()){

					return response()->json([
							'message' => trans('api.promocode_applied') ,
							'code' => 'promocode_applied'
						 ]); 

				}else{
					return back()->with('flash_success', trans('api.promocode_applied'));
				}
			}

		}

		catch (Exception $e) {
			if($request->ajax()){
				return response()->json(['error' => trans('api.something_went_wrong')], 500);
			}else{
				return back()->with('flash_error', trans('api.something_went_wrong'));
			}
		}

	} 

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function upcoming_trips() {
	
		try{
			$UserRequests = UserRequests::UserUpcomingTrips(Auth::user()->id)->get();
			if(!empty($UserRequests)){
				$map_icon = asset('asset/img/marker-start.png');
				foreach ($UserRequests as $key => $value) {
					$UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
							"autoscale=1".
							"&size=320x130".
							"&maptype=terrian".
							"&format=png".
							"&visual_refresh=true".
							"&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
							"&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
							"&path=color:0x000000|weight:3|enc:".$value->route_key.
							"&key=".config('constants.map_key');
				}
			}
			return $UserRequests;
		}

		catch (Exception $e) {
			return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function upcoming_trip_details(Request $request) {

		 $this->validate($request, [
				'request_id' => 'required|integer|exists:user_requests,id',
			]);
	
		try{
			$UserRequests = UserRequests::UserUpcomingTripDetails(Auth::user()->id,$request->request_id)->get();
			if(!empty($UserRequests)){
				$map_icon = asset('asset/img/marker-start.png');
				foreach ($UserRequests as $key => $value) {
					$UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
							"autoscale=1".
							"&size=320x130".
							"&maptype=terrian".
							"&format=png".
							"&visual_refresh=true".
							"&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
							"&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
							"&path=color:0x000000|weight:3|enc:".$value->route_key.
							"&key=".config('constants.map_key');
				}
			}
			return $UserRequests;
		}

		catch (Exception $e) {
			return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
	}


	/**
	 * Show the nearby providers.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function show_providers(Request $request) {

		$this->validate($request, [
				'latitude' => 'required|numeric',
				'longitude' => 'required|numeric',
				'service' => 'numeric|exists:service_types,id',
			]);

		try{

			$distance = config('constants.provider_search_radius', '10');
			$latitude = $request->latitude;
			$longitude = $request->longitude;

			if($request->has('service')){

				$ActiveProviders = ProviderService::AvailableServiceProvider($request->service)
									->get()->pluck('provider_id');

				$Providers = Provider::with('service')->whereIn('id', $ActiveProviders)
					->where('status', 'approved')
					->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
					->get();

			} else {

				$ActiveProviders = ProviderService::where('status', 'active')
									->get()->pluck('provider_id');

				$Providers = Provider::with('service')->whereIn('id', $ActiveProviders)
					->where('status', 'approved')
					->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
					->get();
			}

		
			return $Providers;

		} catch (Exception $e) {
			if($request->ajax()) {
				return response()->json(['error' => trans('api.something_went_wrong')], 500);
			}else{
				return back()->with('flash_error', trans('api.something_went_wrong'));
			}
		}
	}


	/**
	 * Forgot Password.
	 *
	 * @return \Illuminate\Http\Response
	 */


	public function forgot_password(Request $request){

		/*$this->validate($request, [
				'email' => 'required|email|exists:users,email',
			]);*/

		$validator = \Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',            
        ]);

		if ($validator->fails()) {
			return response()->json(['message' => 'The Selected email is invalid'], 422);
		}	
			
		try{  
			
			$user = User::where('email' , $request->email)->first();

			$otp = mt_rand(100000, 999999);

			$user->otp = $otp;
			$user->save();

			Notification::send($user, new ResetPasswordOTP($otp));

			return response()->json([
				'message' => 'OTP sent to your email!',
				'user' => $user
			]);

		}catch(Exception $e){
				return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
	}


	/**
	 * Reset Password.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function reset_password(Request $request){

		$this->validate($request, [
				'password' => 'required|confirmed|min:6',
				'id' => 'required|numeric|exists:users,id'

			]);

		try{

			$User = User::findOrFail($request->id);
			// $UpdatedAt = date_create($User->updated_at);
			// $CurrentAt = date_create(date('Y-m-d H:i:s'));
			// $ExpiredAt = date_diff($UpdatedAt,$CurrentAt);
			// $ExpiredMin = $ExpiredAt->i;
			$User->password = bcrypt($request->password);
			$User->save();
			if($request->ajax()) {
				return response()->json(['message' => trans('api.user.password_updated')]);
			}
		   
			

		}catch (Exception $e) {
			if($request->ajax()) {
				return response()->json(['error' => trans('api.something_went_wrong')], 500);
			}
		}
	}

	/**
	 * help Details.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function help_details(Request $request){

		try{

			if($request->ajax()) {
				return response()->json([
					'contact_number' => config('constants.contact_number',''), 
					'contact_email' => config('constants.contact_email','')
					 ]);
			}

		}catch (Exception $e) {
			if($request->ajax()) {
				return response()->json(['error' => trans('api.something_went_wrong')], 500);
			}
		}
	}   



	/**
	 * Show the wallet usage.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function wallet_passbook(Request $request)
	{
		try{
			$start_node= $request->start_node;
			$limit= $request->limit;
			
			$wallet_transation = UserWallet::where('user_id',Auth::user()->id);
			if(!empty($limit)){
				$wallet_transation =$wallet_transation->offset($start_node);
				$wallet_transation =$wallet_transation->limit($limit);
			}

			$wallet_transation =$wallet_transation->orderBy('id','desc')->get();

			return response()->json(['wallet_transation' => $wallet_transation,'wallet_balance'=>Auth::user()->wallet_balance]);

		} catch (Exception $e) {
			 return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
	}


	/**
	 * Show the promo usage.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function promo_passbook(Request $request)
	{
		try{
			
			return PromocodePassbook::where('user_id',Auth::user()->id)->with('promocode')->get();

		} catch (Exception $e) {
			 
			 return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
	}
	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function test(Request $request)
	{
		 //$push =  (new SendPushNotification)->IncomingRequest($request->id); 
		 $push = (new SendPushNotification)->Arrived($request->id);

		 dd($push);
	}

	 /**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function pricing_logic($id)
	{
	   //return $id;
	   $logic = ServiceType::select('calculator')->where('id',$id)->first();
	   return $logic;

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
			$responsedata=$response->calculateFare($request->all());

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

	/**
	 * Show the wallet usage.
	 *
	 * @return \Illuminate\Http\Response
	 */

	/*public function check(Request $request)
	{

		$this->validate($request, [
				'name' => 'required',
				'age' => 'required',
				'work' => 'required',
			]);
		 return Work::create(request(['name', 'age' ,'work']));
	}*/    

	public function chatPush(Request $request){

		$this->validate($request,[
				'user_id' => 'required|numeric',
				'message' => 'required',
			]);       

		try{

			$user_id=$request->user_id;
			$message=$request->message;
			$sender=$request->sender;

			$message = \PushNotification::Message($message,array(
			'badge' => 1,
			'sound' => 'default',
			'custom' => array('type' => 'chat')
			));

			(new SendPushNotification)->sendPushToProvider($user_id, $message);

			//(new SendPushNotification)->sendPushToUser($user_id, $message);         

			return response()->json(['success' => 'true']);

		} catch(Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

	}

	public function CheckVersion(Request $request){

		$this->validate($request,[
				'sender' => 'in:user,provider',
				'device_type' => 'in:android,ios',
				'version' => 'required',
			]);       

		try{

			$sender=$request->sender;
			$device_type=$request->device_type;
			$version=$request->version;

			if($sender=='user'){
				if($device_type=='ios'){
					$curversion=config('constants.version_ios_user');
					if($curversion==$version){
						return response()->json(['force_update' => false]);
					}
					elseif($curversion>$version){
						return response()->json(['force_update' => true, 'url'=>config('constants.store_link_ios_user')]);
					}
					else{
						return response()->json(['force_update' => false]);
					}
				}
				else{
					$curversion=config('constants.version_android_user');
					if($curversion==$version){
						return response()->json(['force_update' => false]);
					}
					elseif($curversion>$version){                        
						return response()->json(['force_update' => true, 'url'=>config('constants.store_link_android_user')]);
					}
					else{
						return response()->json(['force_update' => false]);
					}
				}
			}
			else{
				if($device_type=='ios'){
					$curversion=config('constants.version_ios_provider');
					if($curversion==$version){
						return response()->json(['force_update' => false]);
					}
					elseif($curversion>$version){                        
						return response()->json(['force_update' => true, 'url'=>config('constants.store_link_ios_provider')]);
					}
					else{
						return response()->json(['force_update' => false]);
					}
				}
				else{
					$curversion=config('constants.version_android_provider');
					if($curversion==$version){
						return response()->json(['force_update' => false]);
					}
					elseif($curversion>$version){
						return response()->json(['force_update' => true, 'url'=>config('constants.store_link_android_provider')]);                        
					}
					else{
						return response()->json(['force_update' => false]);
					}
				}
			}           

		} catch(Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

	}

	public function checkapi(Request $request)
	{
		Log::info('Request Details:', $request->all());
		return response()->json(['sucess' => true]);        
	   
	}


	public function reasons(Request $request)
	{
		$reason = Reason::where('type', 'USER')->where('status', 1)->get();

		return $reason;

	}

	public function payment_log(Request $request)
	{
		$log = PaymentLog::where('transaction_code', $request->order)->first();
		$log->response = $request->all();
        $log->save();

		return response()->json(['message' => trans('api.payment_success')]);

	}

	public function verifyCredentials(Request $request)
	{

		if($request->has("mobile")) {
			$Provider = User::where([['country_code',$request->country_code],['mobile', $request->mobile]])->where('user_type', 'NORMAL')->first();
			if($Provider != null) {
				return response()->json(['message' => trans('api.mobile_exist')], 422);
			} 
		}

		if($request->has("email")) {
			$Provider = User::where('email', $request->input("email"))->first();
			if($Provider != null) {
				return response()->json(['message' => trans('api.email_exist')], 422);
			}
		}

		return response()->json(['message' => trans('api.available')]);

	}

	public function settings(Request $request)
	{
		$serviceType = ServiceType::select('id', 'name')->get();
		$settings = [
			'serviceTypes' => $serviceType,
			'api_key' => config('constants.map_key',''),
			'android_api_key' => config('constants.android_map_key',''),
			'ios_api_key' => config('constants.ios_map_key',''),
			'referral' => [
				'referral' => config('constants.referral', 0),
				'count' => config('constants.referral_count', 0),
				'amount' => config('constants.referral_amount', 0),
				'ride_otp' => (int)config('constants.ride_otp'),
			]
		];
		return response()->json($settings);        
	   
	}

	public function client_token()
    {
        $this->set_Braintree();
        $clientToken = \Braintree_ClientToken::generate();
        return response()->json(['token' => $clientToken]);
    }

	public function set_Braintree(){

        \Braintree_Configuration::environment(config('constants.braintree_environment'));
        \Braintree_Configuration::merchantId(config('constants.braintree_merchant_id'));
        \Braintree_Configuration::publicKey(config('constants.braintree_public_key'));
        \Braintree_Configuration::privateKey(config('constants.braintree_private_key'));
    }

}