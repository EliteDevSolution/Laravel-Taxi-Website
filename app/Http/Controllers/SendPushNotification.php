<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Provider;
use App\ProviderDevice;
use Exception;
use Log;
use Setting;
use App;


use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use App\Notifications\WebPush;

class SendPushNotification extends Controller
{
	/**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function RideAccepted($request){

        $user = User::where('id',$request->user_id)->first();
        $language = $user->language;
        App::setLocale($language);
    	return $this->sendPushToUser($request->user_id, trans('api.push.request_accepted'));
    }

    /**
     * Driver Arrived at your location.
     *
     * @return void
     */
    public function user_schedule($user){
         $user = User::where('id',$user)->first();
         $language = $user->language;
         App::setLocale($language);
        return $this->sendPushToUser($user, trans('api.push.schedule_start'));
    }

    /**
     * New Incoming request
     *
     * @return void
     */
    public function provider_schedule($provider){

        $provider = Provider::where('id',$provider)->with('profile')->first();
        if($provider->profile){
            $language = $provider->profile->language;
            App::setLocale($language);
        }

        return $this->sendPushToProvider($provider, trans('api.push.schedule_start'));

    }

    /**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function UserCancellRide($request){

        if(!empty($request->provider_id)){

            $provider = Provider::where('id',$request->provider_id)->with('profile')->first();

            if($provider->profile){
                $language = $provider->profile->language;
                App::setLocale($language);
            }

            return $this->sendPushToProvider($request->provider_id, trans('api.push.user_cancelled'));
        }
        
        return true;    
    }

    public function ProviderWaiting($user_id, $status){

        $user = User::where('id',$user_id)->first();
        $language = $user->language;
        App::setLocale($language);

        if($status == 1) {
            return $this->sendPushToUser($user_id, trans('api.push.provider_waiting_start'));  
        } else {
            return $this->sendPushToUser($user_id, trans('api.push.provider_waiting_end')); 
        }
        
    }


    /**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function ProviderCancellRide($request){

        $user = User::where('id',$request->user_id)->first();
        $language = $user->language;
        App::setLocale($language);

        return $this->sendPushToUser($request->user_id, trans('api.push.provider_cancelled'));
    }

    /**
     * Driver Arrived at your location.
     *
     * @return void
     */
    public function Arrived($request){

        $user = User::where('id',$request->user_id)->first();
        $language = $user->language;
        App::setLocale($language);

        return $this->sendPushToUser($request->user_id, trans('api.push.arrived'));
    }

    /**
     * Driver Picked You  in your location.
     *
     * @return void
     */
    public function Pickedup($request){
        $user = User::where('id',$request->user_id)->first();
        $language = $user->language;
        App::setLocale($language);

        return $this->sendPushToUser($request->user_id, trans('api.push.pickedup'));
    }

    /**
     * Driver Reached  destination
     *
     * @return void
     */
    public function Dropped($request){

        $user = User::where('id',$request->user_id)->first();
        $language = $user->language;
        App::setLocale($language);

        return $this->sendPushToUser($request->user_id, trans('api.push.dropped').config('constants.currency').$request->payment->total.' by '.$request->payment_mode);
    }

    /**
     * Your Ride Completed
     *
     * @return void
     */
    public function Complete($request){

        $user = User::where('id',$request->user_id)->first();
        $language = $user->language;
        App::setLocale($language);

        return $this->sendPushToUser($request->user_id, trans('api.push.complete'));
    }

    
     
    /**
     * Rating After Successful Ride
     *
     * @return void
     */
    public function Rate($request){

        $user = User::where('id',$request->user_id)->first();
        $language = $user->language;
        App::setLocale($language);

        return $this->sendPushToUser($request->user_id, trans('api.push.rate'));
    }


    /**
     * Money added to user wallet.
     *
     * @return void
     */
    public function ProviderNotAvailable($user_id){
        $user = User::where('id',$user_id)->first();
        $language = $user->language;
        App::setLocale($language);

        return $this->sendPushToUser($user_id,trans('api.push.provider_not_available'));
    }

    /**
     * New Incoming request
     *
     * @return void
     */
    public function IncomingRequest($provider){

        $provider = Provider::where('id',$provider)->with('profile')->first();
        if($provider->profile){
            $language = $provider->profile->language;
            App::setLocale($language);
        }

        return $this->sendPushToProvider($provider->id, trans('api.push.incoming_request'));

    }
    

    /**
     * Driver Documents verfied.
     *
     * @return void
     */
    public function DocumentsVerfied($provider_id){

        $provider = Provider::where('id',$provider_id)->with('profile')->first();
        if($provider->profile){
            $language = $provider->profile->language;
            App::setLocale($language);
        }

        return $this->sendPushToProvider($provider_id, trans('api.push.document_verfied'));
    }


    /**
     * Money added to user wallet.
     *
     * @return void
     */
    public function WalletMoney($user_id, $money){

        $user = User::where('id',$user_id)->first();
        $language = $user->language;
        App::setLocale($language);
        return $this->sendPushToUser($user_id, $money.' '.trans('api.push.added_money_to_wallet'));
    }

    public function ProviderWalletMoney($user_id, $money){

        $user = Provider::where('id',$user_id)->first();
        $language = $user->language;
        App::setLocale($language);

        return $this->sendPushToProvider($user_id, $money.' '.trans('api.push.added_money_to_wallet'));
    }

    /**
     * Money charged from user wallet.
     *
     * @return void
     */
    public function ChargedWalletMoney($user_id, $money){

        $user = User::where('id',$user_id)->first();
        $language = $user->language;
        App::setLocale($language);

        return $this->sendPushToUser($user_id, $money.' '.trans('api.push.charged_from_wallet'));
    }


    public function provider_hold($provider_id){

        $provider = Provider::where('id',$provider_id)->with('profile')->first();
        if($provider->profile){
            $language = $provider->profile->language;
            App::setLocale($language);
        }

        return $this->sendPushToProvider($provider_id, trans('api.push.provider_status_hold'));

    }

    /**
     * Sending Push to a user Device.
     *
     * @return void
     */
    public function sendPushToUser($user_id, $push_message){

    	try{

	    	$user = User::findOrFail($user_id);

            //$user->notify(new WebPush("Notifications", $push_message, url('/')));

            if($user->device_token != ""){

               

    	    	if($user->device_type == 'ios'){

    	    		return \PushNotification::app('IOSUser')
    		            ->to($user->device_token)
    		            ->send($push_message);

    	    	}elseif($user->device_type == 'android'){
    	    		
    	    		return \PushNotification::app('Android')
                        ->to($user->device_token)
                        ->send($push_message);
                       

    	    	}
            }
            


    	} catch(Exception $e){
    		return $e;
    	}

    }

    /**
     * Sending Push to a user Device.
     *
     * @return void
     */
    public function sendPushToProvider($provider_id, $push_message){

    	try{          

	    	$provider = ProviderDevice::where('provider_id',$provider_id)->with('provider')->first();

            $user = Provider::findOrFail($provider_id);

            //$user->notify(new WebPush("Notifications", $push_message, url('/')));           

            if($provider->token != ""){

            	if($provider->type == 'ios'){

            		return \PushNotification::app('IOSProvider')
        	            ->to($provider->token)
        	            ->send($push_message);

            	}elseif($provider->type == 'android'){
            		
            		return \PushNotification::app('Android')
        	            ->to($provider->token)
                        ->send($push_message);
        	            

            	}
            }

            

    	} catch(Exception $e){           
    		return $e;
    	}

    }

}
