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

class WelcomeController extends Controller
{
	
	public function index(){
		return view('newindex');
	}
	





}