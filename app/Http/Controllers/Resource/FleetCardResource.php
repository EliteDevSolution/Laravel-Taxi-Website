<?php

namespace App\Http\Controllers\Resource;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\FleetCard;
use App\Fleet;
use Exception;
use Auth;
use Setting;
use Session;

class FleetCardResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{

            $cards = FleetCard::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->get();
            return $cards; 

        } catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
                'stripe_token' => 'required'
            ]);

        try{

            $customer_id = $this->customer_id();           
            
            $this->set_stripe();              

            $customer = \Stripe\Account::retrieve($customer_id);

            $card = $customer->external_accounts->create(
                 array( "external_account" => $request->stripe_token )
            );
   
            $exist = FleetCard::where('user_id',Auth::user()->id)
                            ->where('last_four',$card['last4'])
                            ->where('brand',$card['brand'])
                            ->count();

            if($exist == 0){

                //delete previous card
                $Fleetcard=Fleetcard::where('user_id',Auth::user()->id)->first();
                
                if(!empty($Fleetcard)){
                    $card_detail = $customer->external_accounts->retrieve($Fleetcard->card_id);

                    if(count($card_detail) > 1)
                    {
                        $card_detail->delete();
                    }

                    Fleetcard::where('card_id',$Fleetcard->card_id)->delete();
                }

                $create_card = new FleetCard;
                $create_card->user_id = Auth::user()->id;
                $create_card->card_id = $card['id'];
                $create_card->last_four = $card['last4'];
                $create_card->brand = $card['brand'];
                $create_card->is_default = '1';
                $create_card->save();

            }else{
                if($request->ajax()){
                    return response()->json(['message' => trans('api.card_already')]); 
                }else{
                    return back()->with('flash_success',trans('api.card_already'));
                }
            }

            if($request->ajax()){
                return response()->json(['message' => trans('api.card_added')]); 
            }else{
                return back()->with('flash_success',trans('api.card_added'));
            }

        } catch(Exception $e){

           if($request->ajax()){
                return response()->json(['error' => $e->getMessage()], 500);
            }else{               
                return redirect()->back()->with('flash_error',$e->getMessage());
            }
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        $this->validate($request,[
                'card_id' => 'required|exists:fleet_cards,card_id,user_id,'.Auth::user()->id,
            ]);

        try{

           $this->set_stripe();

           $customer = \Stripe\Account::retrieve(Auth::user()->stripe_cust_id);

         
         
           $card_detail = $customer->external_accounts->retrieve($request->card_id);
           if(count($card_detail) > 1)

           {
             $card_detail->delete();
           }


           FleetCard::where('card_id',$request->card_id)->delete();

            if($request->ajax()){
                return response()->json(['message' => trans('api.card_deleted')]); 
            }else{
                return back()->with('flash_success',trans('api.card_deleted'));
            }

        } catch(Stripe_CardError $e){
           
            if($request->ajax()){
                return response()->json(['error' => $e->getMessage()], 500);
            }else{
                return back()->with('flash_error',$e->getMessage());
            }
        }
    }

    /**
     * setting stripe.
     *
     * @return \Illuminate\Http\Response
     */
    public function set_stripe(){
        return \Stripe\Stripe::setApiKey(config('constants.stripe_secret_key', ''));
    }

    /**
     * Get a stripe customer id.
     *
     * @return \Illuminate\Http\Response
     */
    public function customer_id()
    {

   

       if(Auth::user()->stripe_cust_id != null){

            return Auth::user()->stripe_cust_id;

        }else{

            try{

               $stripe = $this->set_stripe();

               // $customer = \Stripe\Customer::create([
                //     'email' => Auth::guard('provider')->user()->email,
                // ]);

               $customer= \Stripe\Account::create(array(
                    "country" => "US",
                    "type" => "custom",                    
                    "email" => Auth::user()->email
                ));

                $customer_update = \Stripe\Account::retrieve($customer['id']);
                $customer_update->tos_acceptance->date = time();
                $customer_update->tos_acceptance->ip = $_SERVER['REMOTE_ADDR'];
                $customer_update->legal_entity->business_name =  Auth::user()->first_name.' '.Auth::user()->last_name; 
                $customer_update->legal_entity->dob->day = '27';
                $customer_update->legal_entity->dob->month = '05';
                $customer_update->legal_entity->dob->year= '1990';
                $customer_update->legal_entity->first_name = Auth::user()->first_name;
                $customer_update->legal_entity->last_name =Auth::user()->last_name;
                $customer_update->legal_entity->type = 'individual';
                $customer_update->save();

               Fleet::where('id',Auth::user()->id)->update(['stripe_cust_id' => $customer['id']]);
                return $customer['id'];

            } catch(Exception $e){

                   return $e;
            }
        }
    }

    public function set_default(Request $request)
    {
        try{
            FleetCard::where('user_id',Auth::user()->id)->update(['is_default' => '0']);
            FleetCard::where('id',$request->id)->update(['is_default' => '1']);
            return 'success';
         }
         catch(Exception $e){
            return 'failure';
         }
                   
    }

}
