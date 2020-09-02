<?php

namespace App\Http\Controllers\ProviderAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;

use Setting;
use Validator;
use QrCode;
use App\Helpers\Helper;
use App\Provider;
use App\ProviderService;
use App\Http\Controllers\Resource\ReferralResource;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/provider/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('provider.guest');
        //parent::__construct();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'phone_number' => 'required',
            'country_code' => 'required',
            'email' => 'required|email|max:255|unique:providers',
            'password' => 'required|min:6|confirmed',
            'service_type' => 'required',
            'service_number' => 'required',
            'service_model' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Provider
     */
    protected function create(array $data)
    {   
        if(!empty($data['referral_code'])){
            $validate['referral_unique_id']=$data['referral_code'];       
            $validator  = (new ReferralResource)->checkReferralCode($validate);        
            if (!$validator->fails()) { 
                $validator->errors()->add('referral_code', 'Invalid Referral Code');
                throw new \Illuminate\Validation\ValidationException($validator);
            }   
        }

        $referral_unique_id=(new ReferralResource)->generateCode();
            // QrCode generator
            $file=QrCode::format('png')->generate('{
                "country_code":'.'"'.$data['country_code'].'"'.',
                "phone_number":'.'"'.$data['phone_number'].'"'.'
                }');
            // $file=QrCode::format('png')->size(200)->margin(20)->phoneNumber($data['country_code'].$data['phone_number']);
            $fileName = Helper::upload_qrCode($data['phone_number'],$file);
        if(!empty($data['gender']))
            $gender=$data['gender'];
        else
            $gender='MALE';
        
        if(!empty($data['paypal_email']))
            $paypal_email=$data['paypal_email'];
        else
            $paypal_email=NULL;    

        $Provider = Provider::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'gender' => $gender,
            'country_code' => $data['country_code'],
            'mobile' => $data['phone_number'],
            'password' => bcrypt($data['password']),
            'referral_unique_id' => $referral_unique_id,
            'paypal_email' => $paypal_email,
            'qrcode_url' =>$fileName,           
        ]);

        $provider_service = ProviderService::create([
            'provider_id' => $Provider->id,
            'service_type_id' => $data['service_type'],
            'service_number' => $data['service_number'],
            'service_model' => $data['service_model'],
        ]);

        if(Setting::get('demo_mode', 0) == 1) {
            //$Provider->update(['status' => 'approved']);
            $provider_service->update([
                'status' => 'active',
            ]);
        }

        if(config('constants.send_email', 0) == 1) {
            // send welcome email here
            Helper::site_registermail($Provider);
        }    
        
        //check user referrals
        if(config('constants.referral', 0) == 1) {
            if(!empty($data['referral_code'])){
                //call referral function
                (new ReferralResource)->create_referral($data['referral_code'],$Provider);                
            }
        }
        
        return $Provider;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('provider.auth.register');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('provider');
    }
}
