<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\ServiceType;
use App\Helpers\Helper;
use Setting;
use QrCode;
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
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
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
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
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

        $currentUser = null;

        $email_case = User::where('email', $data['email'])->where([['country_code',$data['country_code']],['mobile', $data['phone_number']]])->first();

        $registeredEmail = User::where('email', $data['email'])->where('user_type', 'INSTANT')->first();
        $registeredMobile = User::where([['country_code',$data['country_code']],['mobile', $data['phone_number']]])->where('user_type', 'INSTANT')->first();

        $registeredEmailNormal = User::where('email', $data['email'])->where('user_type', 'NORMAL')->first();
        $registeredMobileNormal = User::where([['country_code',$data['country_code']],['mobile', $data['phone_number']]])->where('user_type', 'NORMAL')->first();

        $validator  = Validator::make([],[],[]);

        //User Already Exists
        if($email_case != null) {            
            $validator->errors()->add('email', 'User already registered');
            throw new \Illuminate\Validation\ValidationException($validator); 
        }

        if($registeredEmail != null && $registeredMobile != null) {
            //User Already Registerd with same credentials
            if($registeredEmail != null) {       
                $validator->errors()->add('email', 'User already registered with given email-Id!');
                throw new \Illuminate\Validation\ValidationException($validator);
            } else if($registeredMobile != null) {       
                $validator->errors()->add('mobile', 'User already registered with given mobile number!');
                throw new \Illuminate\Validation\ValidationException($validator);
            }

        } else {
            if($registeredEmail != null) $currentUser = $registeredEmail;
            else if($registeredMobile != null) $currentUser = $registeredMobile;
        }

        if($registeredEmailNormal != null) {
            $validator->errors()->add('email', 'User already registered with given email-Id!');
            throw new \Illuminate\Validation\ValidationException($validator); 
        } else if($registeredMobileNormal != null) {
            $validator->errors()->add('mobile', 'User already registered with given mobile number!');
                throw new \Illuminate\Validation\ValidationException($validator);
        } 
            // QrCode generator
            $file=QrCode::format('png')->size(500)->margin(10)->generate('{
                "country_code":'.'"'.$data['country_code'].'"'.',
                "phone_number":'.'"'.$data['phone_number'].'"'.'
                }');
            $fileName = Helper::upload_qrCode($data['phone_number'],$file);
            file_put_contents(public_path().'/'.$fileName,$file);
        if($currentUser == null) {

            $User = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'gender' => (!empty($data['gender'])) ? $data['gender'] : 'MALE' ,
                'country_code' => $data['country_code'],
                'mobile' => $data['phone_number'],
                'password' => bcrypt($data['password']),
                'payment_mode' => 'CASH',
                'user_type' => 'NORMAL',
                'referral_unique_id' => $referral_unique_id,
                'qrcode_url' =>$fileName,           
            ]);
        } else {
            $User = $currentUser;
            $User->first_name = $data['first_name'];
            $User->last_name = $data['last_name'];
            $User->email = $data['email'];
            $User->gender = (!empty($data['gender'])) ? $data['gender'] : 'MALE';
            $User->country_code = $data['country_code'];
            $User->mobile = $data['phone_number'];
            $User->password = bcrypt($data['password']);
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
            if(!empty($data['referral_code'])){
                //call referral function
                (new ReferralResource)->create_referral($data['referral_code'],$User);                
            }
        }    

        return $User;
    }

    
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('user.auth.register');
    }

    public function ride()
    {
        $services = ServiceType::get();
        return view('ride' , compact('services'));
    }
}
