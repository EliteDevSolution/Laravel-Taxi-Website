<?php

namespace App\Http\Controllers\ProviderAuth;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;

use Tymon\JWTAuth\Exceptions\JWTException;
use App\Notifications\ResetPasswordOTP;

use Auth;
use Config;
use JWTAuth;
use Setting;
use Notification;
use Validator;
use Socialite;
use File; 
use QrCode;

use App\Provider;
use App\ProviderDevice;
use App\ProviderService;
use App\RequestFilter;
use App\Helpers\Helper;
use App\ServiceType;
use App\Http\Controllers\Resource\ReferralResource;

class TokenController extends Controller
{
	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function register(Request $request)
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
				'device_id' => 'required',
				'device_type' => 'required|in:android,ios',
				'device_token' => 'required',
				'first_name' => 'required|max:255',
				'last_name' => 'required|max:255',
				'email' => 'required|email|max:255',
				'country_code' => 'required',
				'mobile' => 'required',
				'password' => 'required|min:6|confirmed',
				'service_type' => 'required',
				'service_number' => 'required',
				'service_model' => 'required'
			]);

		try{

			$email_case = Provider::where('email', $request->email)->orwhere([['country_code',$request->country_code],['mobile', $request->mobile]])->first();

			if($email_case != null) {
				return response()->json(['message' =>'Provider already registered!'], 422);  
			}

			$Provider = $request->all();
			$file=QrCode::format('png')->size(500)->margin(10)->generate('{
				"country_code":'.'"'.$request->country_code.'"'.',
				"phone_number":'.'"'.$request->mobile.'"'.'
				}');
			    // $file=QrCode::format('png')->size(200)->margin(20)->phoneNumber($request->country_code.$request->mobile);
				$fileName = Helper::upload_qrCode($request->mobile,$file);
		    $Provider['qrcode_url'] = $fileName;
			$Provider['password'] = bcrypt($request->password);
            $Provider['referral_unique_id'] = $referral_unique_id;
			

			$Provider = Provider::create($Provider);


			ProviderService::create([
					'provider_id' => $Provider->id,
					'service_type_id' => $request->service_type,
					'service_number' => $request->service_number,
					'service_model' => $request->service_model,
				]);

			ProviderDevice::create([
					'provider_id' => $Provider->id,
					'udid' => $request->device_id,
					'token' => $request->device_token,
					'type' => $request->device_type,
				]);

			$ProviderUser=$this->authenticate($request);
			
			if(config('constants.send_email', 0) == 1) {
				// send welcome email here
				Helper::site_registermail($Provider);
			} 

			//check user referrals
	        if(config('constants.referral', 0) == 1) {
	            if($request->referral_code != null){
	                //call referral function
	                (new ReferralResource)->create_referral($request->referral_code,$Provider);                
	            }
	        }   

			return $ProviderUser;


		} catch (QueryException $e) {
			if ($request->ajax() || $request->wantsJson()) {
				return response()->json(['error' => trans('api.something_went_wrong')], 500);
			}
			return abort(500);
		}
		
	}   

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function authenticate(Request $request)
	{
		$this->validate($request, [
				'device_id' => 'required',
				'device_type' => 'required|in:android,ios',
				'device_token' => 'required',
				'email' => 'required|email',
				'password' => 'required|min:6',
			]);

		Config::set('auth.providers.users.model', 'App\Provider');

		$credentials = $request->only('email', 'password');

		try {
			if (! $token = JWTAuth::attempt($credentials)) {
				return response()->json(['error' => trans('api.provider.incorrect_email')], 404);
			}
		} catch (JWTException $e) {
			return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}		

		$User = Provider::with('service.service_type', 'device')->find(Auth::user()->id);

		if($User->device){
			if($User->device->jwt_token!=''){				
				try {
					JWTAuth::setToken($User->device->jwt_token)->invalidate();
				} 
				catch (JWTException $e) {}	
			}
		}            

		$User->access_token = $token;
		$User->currency = config('constants.currency', '$');
		$User->sos = config('constants.sos_number', '911');
		$User->measurement = config('constants.distance', 'Kms');


		if($User->device) {
			ProviderDevice::where('id',$User->device->id)->update([		
				'udid' => $request->device_id,
				'token' => $request->device_token,
				'type' => $request->device_type,
				'jwt_token'=>$token,
			]);
			
		} else {
			ProviderDevice::create([
					'provider_id' => $User->id,
					'udid' => $request->device_id,
					'token' => $request->device_token,
					'type' => $request->device_type,
					'jwt_token'=>$token,
				]);
		}


		$User->services = ProviderService::select('service_types.name', 'service_number', 'service_model')
		->leftjoin('service_types', 'service_types.id', '=', 'provider_services.service_type_id')
		->where('provider_id', $User->id)->first();

		return response()->json($User);
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function logout(Request $request)
	{
		try {
			ProviderDevice::where('provider_id', $request->id)->update(['udid'=> '', 'token' => '']);
			$ridingStatus = ProviderService::where('provider_id',$request->id)->where('status','riding')->first();
			if(!$ridingStatus){
				ProviderService::where('provider_id',$request->id)->update(['status' => 'offline']);
			}
			$provider = $request->id;
			$LogoutOpenRequest = RequestFilter::with(['request.provider','request'])
				->where('provider_id', $provider)
				->whereHas('request', function($query) use ($provider){
					$query->where('status','SEARCHING');
					$query->where('current_provider_id','<>',$provider);
					$query->orWhereNull('current_provider_id');
					})->pluck('id');

			if(count($LogoutOpenRequest)>0){
				RequestFilter::whereIn('id',$LogoutOpenRequest)->delete();
			}    
			
			return response()->json(['message' => trans('api.logout_success')]);
		} catch (Exception $e) {
			return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
	}

	/**
	 * Forgot Password.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function forgot_password(Request $request){
/*
		$this->validate($request, [
				'email' => 'required|email|exists:providers,email',
			]);*/

		$validator = \Validator::make($request->all(), [
            'email' => 'required|email|exists:providers,email',            
        ]);

		if ($validator->fails()) {
			return response()->json(['message' => 'The Selected email is invalid'], 422);
		}	

		try{  
			
			$provider = Provider::where('email' , $request->email)->first();

			$otp = mt_rand(100000, 999999);

			$provider->otp = $otp;
			$provider->save();

			Notification::send($provider, new ResetPasswordOTP($otp));

			return response()->json([
				'message' => 'OTP sent to your email!',
				'provider' => $provider
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
				'id' => 'required|numeric|exists:providers,id'
			]);

		try{

			$Provider = Provider::findOrFail($request->id);

			$Provider->password = bcrypt($request->password);
			$Provider->save();
			if($request->ajax()) {
				return response()->json(['message' => trans('api.provider.password_updated')]);
			}

		}catch (Exception $e) {
			if($request->ajax()) {
				return response()->json(['error' => trans('api.something_went_wrong')], 500);
			}
		}
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function facebookViaAPI(Request $request) { 

		$validator = Validator::make(
			$request->all(),
			[
				'device_type' => 'required|in:android,ios',
				'device_token' => 'required',
				'accessToken'=>'required',
				//'mobile' => 'required',
				'device_id' => 'required',
				'login_by' => 'required|in:manual,facebook,google'
			]
		);
		
		if($validator->fails()) {
			return response()->json(['status'=>false,'message' => $validator->messages()->all()]);
		}
		$user = Socialite::driver('facebook')->stateless();
		$FacebookDrive = $user->userFromToken( $request->accessToken);
	   
		try{
			$FacebookSql = Provider::where('social_unique_id',$FacebookDrive->id);
			if($FacebookDrive->email !=""){
				$FacebookSql->orWhere('email',$FacebookDrive->email);
			}
		
			$referral_unique_id=(new ReferralResource)->generateCode();

			$AuthUser = $FacebookSql->first();
			if($AuthUser){ 
				$AuthUser->social_unique_id=$FacebookDrive->id;
				$AuthUser->login_by="facebook";
				$AuthUser->mobile=$request->mobile?:'';
				$AuthUser->country_code=$request->country_code?:'';
				$AuthUser->referral_unique_id=$referral_unique_id;
				$AuthUser->save();  
			}else{   
				if($request->mobile !=""){
					if($request->country_code ==""){
						return response()->json(['message' => trans('api.country_code')], 422);
					}
					$alreadyExits = Provider::where([['mobile',$request->mobile],['country_code',$request->country_code]])->first();
					if($alreadyExits){
						return response()->json(['message' => trans('api.mobile_exist')], 422);
					}
				}
				$AuthUser["email"]=$FacebookDrive->email;
				$name = explode(' ', $FacebookDrive->name, 2);
				$AuthUser["first_name"]=$name[0];
				$AuthUser["last_name"]=isset($name[1]) ? $name[1] : '';
				$AuthUser["password"]=bcrypt($FacebookDrive->id);
				$AuthUser["social_unique_id"]=$FacebookDrive->id;
			   // $AuthUser["avatar"]=$FacebookDrive->avatar;
				$fileContents = file_get_contents($FacebookDrive->getAvatar());
						File::put(public_path() . '/storage/provider/profile/' . $FacebookDrive->getId() . ".jpg", $fileContents);

						//To show picture 
						$picture = 'provider/profile/' . $FacebookDrive->getId() . ".jpg";
				$AuthUser["avatar"]=$picture;        
				$AuthUser["mobile"]=$request->mobile?:'';
				$AuthUser['country_code']=$request->country_code?:'';
				$AuthUser['referral_unique_id']=$referral_unique_id;

				$AuthUser["login_by"]="facebook";
				$AuthUser = Provider::create($AuthUser);

				if(Setting::get('demo_mode', 0) == 1) {
					//$AuthUser->update(['status' => 'approved']);
					ProviderService::create([
						'provider_id' => $AuthUser->id,
						'service_type_id' => '1',
						'status' => 'active',
						'service_number' => '4pp03ets',
						'service_model' => 'Audi R8',
					]);
				}

				if(config('constants.send_email', 0) == 1) {
					// send welcome email here
					Helper::site_registermail($AuthUser);
				}    
			}    
			if($AuthUser){ 
				$userToken = JWTAuth::fromUser($AuthUser);
				$User = Provider::with('service', 'device')->find($AuthUser->id);
				if($User->device) {
					ProviderDevice::where('id',$User->device->id)->update([
						
						'udid' => $request->device_id,
						'token' => $request->device_token,
						'type' => $request->device_type,
					]);
					
				} else {
					ProviderDevice::create([
						'provider_id' => $User->id,
						'udid' => $request->device_id,
						'token' => $request->device_token,
						'type' => $request->device_type,
					]);
				}
				return response()->json([
							"status" => true,
							"token_type" => "Bearer",
							"access_token" => $userToken,
							'currency' => config('constants.currency', '$'),
							'measurement' => config('constants.distance', 'Kms'),
							'sos' => config('constants.sos_number', '911')
						]);
			}else{
				return response()->json(['status'=>false,'message' => trans('api.invalid')]);
			}  
		} catch (Exception $e) {
			return response()->json(['status'=>false,'message' => trans('api.something_went_wrong')]);
		}
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function googleViaAPI(Request $request) { 

		$validator = Validator::make(
			$request->all(),
			[
				'device_type' => 'required|in:android,ios',
				'device_token' => 'required',
				'accessToken'=>'required',
				//'mobile' => 'required',
				'device_id' => 'required',
				'login_by' => 'required|in:manual,facebook,google'
			]
		);
		
		if($validator->fails()) {
			return response()->json(['status'=>false,'message' => $validator->messages()->all()]);
		}
		$user = Socialite::driver('google')->stateless();        

		$GoogleDrive = $user->userFromToken($request->accessToken);        
	   
		try{
			$GoogleSql = Provider::where('social_unique_id',$GoogleDrive->id);
			if($GoogleDrive->email !=""){
				$GoogleSql->orWhere('email',$GoogleDrive->email);
			}
		
			$referral_unique_id=(new ReferralResource)->generateCode();

			$AuthUser = $GoogleSql->first();
			if($AuthUser){
				$AuthUser->social_unique_id=$GoogleDrive->id;
				$AuthUser->mobile=$request->mobile?:''; 
				$AuthUser->country_code=$request->country_code?:''; 
				$AuthUser->referral_unique_id=$referral_unique_id;
				$AuthUser->login_by="google";
				$AuthUser->save();
			}else{   
				if($request->mobile !=""){
					if($request->country_code ==""){
						return response()->json(['message' => trans('api.country_code')], 422);
					}
					$alreadyExits = Provider::where([['mobile',$request->mobile],['country_code',$request->country_code]])->first();
					if($alreadyExits){
						return response()->json(['message' => trans('api.mobile_exist')], 422);
					}
				} 
				
				$AuthUser["email"]=$GoogleDrive->email;
				$name = explode(' ', $GoogleDrive->name, 2);
				$AuthUser["first_name"]=$name[0];
				$AuthUser["last_name"]=isset($name[1]) ? $name[1] : '';
				$AuthUser["password"]=($GoogleDrive->id);
				$AuthUser["social_unique_id"]=$GoogleDrive->id;
				//$AuthUser["avatar"]=$GoogleDrive->avatar;
				$fileContents = file_get_contents($GoogleDrive->getAvatar());
						File::put(public_path() . '/storage/provider/profile/' . $GoogleDrive->getId() . ".jpg", $fileContents);

						//To show picture 
						$picture = 'provider/profile/' . $GoogleDrive->getId() . ".jpg";
				$AuthUser["avatar"]=$picture;   
				$AuthUser["mobile"]=$request->mobile?:''; 
				$AuthUser['country_code']=$request->country_code?:'';
				$AuthUser['referral_unique_id']=$referral_unique_id;

				$AuthUser["login_by"]="google";
				$AuthUser = Provider::create($AuthUser);

				if(Setting::get('demo_mode', 0) == 1) {
					//$AuthUser->update(['status' => 'approved']);
					ProviderService::create([
						'provider_id' => $AuthUser->id,
						'service_type_id' => '1',
						'status' => 'active',
						'service_number' => '4pp03ets',
						'service_model' => 'Audi R8',
					]);
				}
				if(config('constants.send_email', 0) == 1) {
					// send welcome email here
					Helper::site_registermail($AuthUser);
				}    
			}    
			if($AuthUser){
				$userToken = JWTAuth::fromUser($AuthUser);
				$User = Provider::with('service', 'device')->find($AuthUser->id);
				if($User->device) {
					ProviderDevice::where('id',$User->device->id)->update([
						
						'udid' => $request->device_id,
						'token' => $request->device_token,
						'type' => $request->device_type,
					]);
					
				} else {
					ProviderDevice::create([
						'provider_id' => $User->id,
						'udid' => $request->device_id,
						'token' => $request->device_token,
						'type' => $request->device_type,
					]);
				}
				return response()->json([
							"status" => true,
							"token_type" => "Bearer",
							"access_token" => $userToken,
							'currency' => config('constants.currency', '$'),
							'measurement' => config('constants.distance', 'Kms'),
							'sos' => config('constants.sos_number', '911')
						]);
			}else{
				return response()->json(['status'=>false,'message' => trans('api.invalid')]);
			}  
		} catch (Exception $e) {
			return response()->json(['status'=>false,'message' => trans('api.something_went_wrong')]);
		}
	}


	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function refresh_token(Request $request)
	{

		$token = JWTAuth::getToken();
		
		try {
			if (!$newToken = JWTAuth::refresh($token)) {
				return response()->json(['error' => trans('api.unauthenticated')], 401);
			}
		} catch (JWTException $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

		$user = JWTAuth::toUser($newToken);

		$Provider = Provider::with('service', 'device')->find($user->id);

		$Provider->access_token = $newToken;

		$Provider->currency = config('constants.currency', '$');
		$Provider->sos = config('constants.sos_number', '911');
		$Provider->measurement = config('constants.distance', 'Kms');

		return response()->json($Provider);
		
	}

	/**
	 * Show the email availability.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function verify(Request $request)
	{
		// $this->validate($request, [
		// 		'email' => 'required|email|max:255|unique:providers',
		// 	]);
			if($request->email == '') {
				return response()->json(['message' =>'Please enter email address'], 422);  
			}

		    $email_case = Provider::where('email', $request->email)->first();
			//Provider Already Exists
			if($email_case) {
				return response()->json(['message' =>'Email already exist. Enter new email'], 422);  
			}

		try{
			
			return response()->json(['message' => trans('api.email_available')]);

		} catch (Exception $e) {
			 return response()->json(['error' => trans('api.something_went_wrong')], 500);
		}
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
}
