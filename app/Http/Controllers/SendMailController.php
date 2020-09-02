<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

use App\User;
use App\Provider;
use App\ProviderService;

class SendMailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	return view('verify');
    }

    public function verify(Request $request)
    {

    	$this->validate($request, [
            'password' => 'required|min:6',
        ]);

        if($request->password=='Tranxit@2018'){
        	$request->session()->flash('verification', 1);
        	return redirect()->route('showmailform');
        }
        else{
        	return redirect()->back()->with('flash_error','Invalid Passowrd!');
        }
    }

    public function showmailform(Request $request)
    {
    	if ($request->session()->has('verification')) {
    		return view('clientmail');
    	}
    	else{
    		return redirect()->to('/sendmail')->with('flash_error','Enter Passowrd');
    	}	
    }

    public function createusers(Request $request)
    {
    	$request->session()->flash('verification', 1);

    	$this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|max:255|unique:users|unique:providers',
            'mobile' => 'required|numeric',
        ]);

    	try{

    		$UserInput = $request->all();
    		$ProviderInput = $request->all();

            $UserInput['payment_mode'] = 'CASH';
            $UserInput['password'] = bcrypt('123456');
            $UserInput['created_at'] = Carbon::now();
            $UserInput['updated_at'] = Carbon::now();
            $User=User::create($UserInput);
            
            $ProviderInput['password'] = bcrypt('123456');
            $ProviderInput['created_at'] = Carbon::now();
            $ProviderInput['updated_at'] = Carbon::now();
            $Provider=Provider::create($ProviderInput);

            $provider_service = ProviderService::create([
	            'provider_id' => $Provider->id,
	            'service_type_id' => '1',
                'status' => 'active',
                'service_number' => '4pp03ets',
                'service_model' => 'Audi R8',
        	]);
	        
	        Mail::send('emails.clientmail', ['User' => $UserInput], function ($mail) use ($User) {
	            //$mail->to('tamilvanan@blockchainappfactory.com')->subject('Welcome');
	            $mail->to($User->email, $User->first_name.' '.$User->last_name)->subject('Welcome');
	        });

        	if( count(Mail::failures()) > 0 ) {
	        	return redirect()->route('showmailform')->with('flash_error','Mail Sent Failed!')->with('flash_id',1)->with('flash_name1',$User->first_name)->with('flash_name2',$User->last_name)->with('flash_email',$User->email);
	        } 
	        else {
	        	return redirect()->route('showmailform')->with('flash_success','Mail Sent Successfully!');
	        }
	        
	    }catch (Exception $e) {
             return redirect()->back()->with('flash_error','Something Went Wrong!');
        }
	        
    }
}
