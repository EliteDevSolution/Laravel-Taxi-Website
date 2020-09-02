<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Eventcontact;

use App\User;
use App\PushSubscription;
use App\ServiceType;
use App\UserWallet;
use App\Notifications;
use App\UserRequestLostItem;
use App\Dispute;
use App\UserRequestDispute;
use App\UserRequests;
use Auth;
use Setting;
use App\Helpers\Helper;
use App\Http\Controllers\Resource\ReferralResource;

class HomeController extends Controller
{
    protected $UserAPI;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserApiController $UserAPI)
    {
        $this->middleware('auth', ['except' => ['save_subscription']]);
        $this->middleware('demo', ['only' => [
                'update_password',
            ]]);
        $this->UserAPI = $UserAPI;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Response = $this->UserAPI->request_status_check()->getData();
        
        if(empty($Response->data))
        { 
         $services = $this->UserAPI->services();
            return view('user.dashboard',compact('services'));
        }else{
            return view('user.ride.waiting')->with('request',$Response->data[0]);
        }
    }

    /**
     * Show the application profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return view('user.account.profile');
    }

    /**
     * Show the application profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit_profile()
    {
        return view('user.account.edit_profile');
    }

    /**
     * Update profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function update_profile(Request $request)
    {
        return $this->UserAPI->update_profile($request);
    }

    /**
     * Show the application change password.
     *
     * @return \Illuminate\Http\Response
     */
    public function change_password()
    {
        return view('user.account.change_password');
    }

    /**
     * Change Password.
     *
     * @return \Illuminate\Http\Response
     */
    public function update_password(Request $request)
    {
        return $this->UserAPI->change_password($request);
    }

    /**
     * Trips.
     *
     * @return \Illuminate\Http\Response
     */
    public function trips()
    {
        $trips = $this->UserAPI->trips();
        return view('user.ride.trips',compact('trips'));
    }
    

     /**
     * Payment.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment()
    {
        $cards = (new Resource\CardResource)->index();
        return view('user.account.payment',compact('cards'));
    }


    /**
     * Wallet.
     *
     * @return \Illuminate\Http\Response
     */
    public function wallet(Request $request)
    {
        $cards = (new Resource\CardResource)->index();

        $wallet_transation = UserWallet::where('user_id',Auth::user()->id)->orderBy('id','desc')
                                ->paginate(config('constants.per_page', '10'));
            
        $pagination=(new Helper)->formatPagination($wallet_transation);

        if(config('constants.braintree') == 1) {
            $this->UserAPI->set_Braintree();
            $clientToken = \Braintree_ClientToken::generate();
        } else {
            $clientToken = '';
        }
        
        return view('user.account.wallet',compact('wallet_transation','pagination','cards','clientToken'));
    }

    /**
     * Promotion.
     *
     * @return \Illuminate\Http\Response
     */
    public function promotions_index(Request $request)
    {
        $promocodes = $this->UserAPI->promocodes();
        return view('user.account.promotions', compact('promocodes'));
    }

    /**
     * Add promocode.
     *
     * @return \Illuminate\Http\Response
     */
    public function promotions_store(Request $request)
    {
        return $this->UserAPI->add_promocode($request);
    }

    /**
     * Upcoming Trips.
     *
     * @return \Illuminate\Http\Response
     */
    public function upcoming_trips()
    {
        $trips = $this->UserAPI->upcoming_trips();
        return view('user.ride.upcoming',compact('trips'));
    }

    public function incoming()
    {
        $Response = $this->UserAPI->request_status_check()->getData();
        
        if(empty($Response->data))
        { 
            return response()->json(['status' => 0]);
        }else{
            return response()->json(['status' => 1]);
        }
    }

    public function referral()
    {
        if(config('constants.referral') == 0){
            return redirect('dashboard');
        }
        
        $referrals  = (new ReferralResource)->get_referral(1,Auth::user()->id);        
        return view('user.referral',compact('referrals'));
    }
      /**
     * Notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function notifications()
    {
        $notifications = Notifications::where([['notify_type','!=','provider'],['status','active']])
                                       ->orderBy('created_at' , 'desc')
                                       ->get();
        return view('user.notification.index',compact('notifications'));
    }
      /**
     * Lost iteam.
     *
     * @return \Illuminate\Http\Response
     */
    public function lostitem($id)
    {

        $lostitem = UserRequestLostItem::where('request_id',$id)
                                        ->get();
         $closedStatus = UserRequestLostItem::where([['request_id',$id],['status','closed']])
                                        ->first();
              $sendBtn = ($closedStatus)?"yes":"no";                         
    return response()->json(['lostitem' => $lostitem,'sendBtn' => $sendBtn]);
    }
      /**
     * Lost Iteam Save.
     *
     * @return \Illuminate\Http\Response
     */
    public function lostitem_store(Request $request)
    {
            try{
    
                $LostItem = new UserRequestLostItem;
                $LostItem->request_id = $request->request_id;
                $LostItem->user_id = Auth::user()->id;                    
                $LostItem->lost_item_name = $request->lost_item_name;
                $LostItem->comments_by = 'user';
                if($request->has('comments')) {
                    $LostItem->comments = $request->comments;
                }    

                $LostItem->save();
    
                if($request->ajax()){
                    return response()->json(['message' => trans('user.ride.trips.saved')]);
                }else{
                    return back()->with('flash_success', trans('user.ride.trips.saved'));
                }
    
            } 
    
            catch (ModelNotFoundException $e) {
                return back()->with('flash_error', trans('user.ride.trips.not_found'));
            }
       
    }
      /**
     * Dispute.
     *
     * @return \Illuminate\Http\Response
     */
    public function dispute($id)
    {

         $dispute = UserRequestDispute::where([['request_id',$id],['dispute_type','!=','provider']])
                                        ->get();
         $closedStatus = UserRequestDispute::where([['request_id',$id],['status','closed'],['dispute_type','!=','provider']])
                                        ->first();
         $disputeReason = Dispute::where([['dispute_type','user'],['status','active']])
                                        ->get();
              $sendBtn = ($closedStatus)?"yes":"no";                         
    return response()->json(['dispute' => $dispute,'sendBtn' => $sendBtn,'disputeReason'=>$disputeReason]);
    }
      /**
     * Dispute Save.
     *
     * @return \Illuminate\Http\Response
     */
    public function dispute_store(Request $request)
    {
            try{
                $dispute = new UserRequestDispute;
                $dispute->request_id = $request->request_id;
                $dispute->user_id = Auth::user()->id;                    
                $dispute->dispute_title = $request->dispute_title;
                $dispute->dispute_name = $request->dispute_name;
                $dispute->dispute_type = 'user';
                if($request->has('comments')) {
                    $dispute->comments = $request->comments;
                }    
    
                $dispute->save();
    
                if($request->ajax()){
                    return response()->json(['message' => trans('user.ride.trips.saved')]);
                }else{
                    return back()->with('flash_success', trans('user.ride.trips.saved'));
                }
    
            } 
    
            catch (ModelNotFoundException $e) {
                return back()->with('flash_error', trans('user.ride.trips.not_found'));
            }
       
    }

    public function track($id) {

         $ride = UserRequests::select('user_requests.s_latitude', 'user_requests.s_longitude', 'users.first_name', 'users.last_name')->leftjoin('users', 'users.id', '=', 'user_requests.user_id')->where('user_requests.id', $id)->first();

         if($ride != null) {
            return view('track', compact('ride', 'id'));
         }
        
        abort(404);

    }

    public function track_location(Request $request) {


        $ride = UserRequests::select('user_requests.track_latitude AS s_latitude', 'user_requests.track_longitude AS s_longitude', 'user_requests.d_latitude', 'user_requests.d_longitude', 'service_types.marker')->leftjoin('service_types', 'service_types.id', '=', 'user_requests.service_type_id')->where('user_requests.id', $request->id)->where('user_requests.status', 'PICKEDUP')->first();

        if($ride != null) {
            $s_latitude = $ride->s_latitude;
            $s_longitude = $ride->s_longitude;
            $d_latitude = $ride->d_latitude;
            $d_longitude = $ride->d_longitude;

            $apiurl = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$s_latitude.",".$s_longitude."&destinations=".$d_latitude.",".$d_longitude."&mode=driving&sensor=false&units=imperial&key=".config('constants.map_key');

            $client = new \GuzzleHttp\Client;
            $location = $client->get($apiurl);           
            $location = json_decode($location->getBody(),true);

            if(!empty($location['rows'][0]['elements'][0]['status']) && $location['rows'][0]['elements'][0]['status']=='OK'){

                $meters = $location['rows'][0]['elements'][0]['distance']['value'];
                $source = $s_latitude . ',' . $s_longitude;
                $destination = $d_latitude . ',' . $d_longitude;
                $minutes = $location['rows'][0]['elements'][0]['duration']['value'];

            }

            return response()->json(['meters' => $meters, 'source' => $source, 'destination' => $destination, 'minutes' => $minutes, 'marker' => $ride->marker ]);
        }
        

        return response()->json([ 'status' => 'Data not available' ], 201);
    }

    public function save_subscription($id, $guard, Request $request) {

        $user = User::findOrFail($id);

        $endpoint = $request->input('endpoint');
        $key = $request->input('keys.p256dh');
        $token = $request->input('keys.auth');
        $subscription_use = null;

        $subscription = PushSubscription::findByEndpoint($endpoint);

        if ($subscription && $subscription->admin_id == $id) {
            $subscription->public_key = $key;
            $subscription->auth_token = $token;
            $subscription->save();

            return $subscription;
        }

        if ($subscription && ! $subscription->admin_id == $id) {
            $subscription->delete();
        }

        $subscribe = new PushSubscription();
        $subscribe->admin_id = $id;
        $subscribe->endpoint = $endpoint;
        $subscribe->public_key = $key;
        $subscribe->auth_token = $token;
        $subscribe->save();

        return response()->json([ 'success' => true ]);
    }
   
}
