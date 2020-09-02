<?php

namespace App\Http\Controllers;

use App\AdminWallet;
use App\Card;
use App\PaystackCard;
use App\Fleet;
use App\Helpers\PaytmLibrary;
use App\Http\Controllers\ProviderResources\TripController;
use App\Http\Controllers\SendPushNotification;
use App\Provider;
use App\PaymentLog;
use App\ProviderCard;
use App\ProviderStripeCard;
use App\ProviderWallet;
use App\Services\PaymentGateway;
use App\User;
use App\UserRequestPayment;
use App\UserRequests;
use App\UserWallet;
use App\WalletRequests;
use Auth;
use Exception;
use Illuminate\Http\Request;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Stripe\Stripe;
use Tzsk\Payu\Facade\Payment as PayuPayment;
use Paystack;

class PaymentController extends Controller
{
    /**
     * payment for user.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment(Request $request)
    {

        $this->validate($request, [
        'request_id' => 'required|exists:user_request_payments,request_id|exists:user_requests,id,paid,0,user_id,'.Auth::user()->id
        ]);

        $UserRequest = UserRequests::find($request->request_id);

        $paymentMode = $request->has('payment_mode') ? strtoupper($request->payment_mode) : $UserRequest->payment_mode;

        $tip_amount = 0;

        $random = config('constants.booking_prefix').mt_rand(100000, 999999);

        if ($paymentMode != 'CASH') {

            $RequestPayment = UserRequestPayment::where('request_id', $request->request_id)->first();

            if (isset($request->tips) && !empty($request->tips)) {
                $tip_amount = round($request->tips, 2);
            }

            $totalAmount = $RequestPayment->payable + $tip_amount;

            if ($totalAmount == 0) {

                $UserRequest->payment_mode = 'CARD';
                $RequestPayment->card = $RequestPayment->payable;
                $RequestPayment->payable = 0;
                $RequestPayment->tips = $tip_amount;
                $RequestPayment->provider_pay = $RequestPayment->provider_pay + $tip_amount;
                $RequestPayment->save();

                $UserRequest->paid = 1;
                $UserRequest->status = 'COMPLETED';
                $UserRequest->save();

                //for create the transaction
                (new TripController)->callTransaction($request->request_id);

                if ($request->ajax()) {
                    return response()->json(['message' => trans('api.paid')]);
                } else {
                    return redirect('dashboard')->with('flash_success', trans('api.paid'));
                }

            } else {

                $log = new PaymentLog();
                $log->user_type = 'user';
                $log->transaction_code = $random;
                $log->amount = $totalAmount;
                $log->transaction_id = $UserRequest->id;
                $log->payment_mode = $paymentMode;
                $log->user_id = \Auth::user()->id;
                $log->save();

                switch ($paymentMode) {
                  case 'BRAINTREE':

                    $gateway = new PaymentGateway('braintree');

                    return $gateway->process([
                        'amount' => $totalAmount,
                        'nonce' => $UserRequest->braintree_nonce,
                        'order' => $random,
                    ]);

                    break;

                  //case 'STRIPE':
                  case 'CARD':

                    $Card = Card::where('user_id', Auth::user()->id)->where('is_default', 1)->first();

                    if($Card == null)  $Card = Card::where('user_id', Auth::user()->id)->first();

                    $gateway = new PaymentGateway('stripe');
                    return $gateway->process([
                        'order' => $random,
                        "amount" => $totalAmount,
                        "currency" => config('constants.stripe_currency'),
                        "customer" => Auth::user()->stripe_cust_id,
                        "card" => $Card->card_id,
                        "description" => "Payment Charge for " . Auth::user()->email,
                        "receipt_email" => Auth::user()->email,
                    ]);

                    break;


                  case 'CARD1':

                    $Card = PaystackCard::where('user_id', Auth::user()->id)->where('is_default', 1)->first();

                    if($Card == null)  $Card = PaystackCard::where('user_id', Auth::user()->id)->first();

                    //PayStack Card Payment.
                        $gateway = new PaymentGateway('paystack');
                        return $gateway->process([
                            "order" => $random,
                            "amount" => $totalAmount,
                            "currency" => config('constants.paystack_currency'),
                            "customer" => Auth::user()->stripe_cust_id, 
                            "card" => $Card->card_id,
                            "description" => "Payment Charge for " . Auth::user()->email,
                            "receipt_email" => Auth::user()->email,
                        ]);
                
                    break;

                  case 'PAYUMONEY':

                    if ($request->ajax()) {

                        $paramList = [
                            'key' => config('constants.payumoney_key'),
                            'txnid' => $random,
                            'amount' => $totalAmount,
                            'productinfo' => $random,
                            'firstname' => Auth::user()->first_name,
                            'email' => Auth::user()->email,
                            'phone' => Auth::user()->mobile,
                        ];

                        $paramList['surl'] = url('api/user/payu/success');
                        $paramList['curl'] = url('api/user/payu/failure');
                        $paramList['service_provider'] = 'payumoney';
                        $paramList['merchant_id'] = config('constants.payumoney_merchant_id');
                        $paramList['payu_salt'] = config('constants.payumoney_salt');

                        $hash = '';
                        // Hash Sequence
                        $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";

                        $hashVarsSeq = explode('|', $hashSequence);
                        $hash_string = '';
                        foreach ($hashVarsSeq as $hash_var) {
                            $hash_string .= isset($paramList[$hash_var]) ? $paramList[$hash_var] : '';
                            $hash_string .= '|';
                        }

                        $hash_string .= config('constants.payumoney_salt');

                        $paramList['hash_string'] = $hash_string;
                        $paramList['hash'] = hash('sha512', $hash_string);

                        return response()->json($paramList, 200);

                    }

                    $gateway = new PaymentGateway('payumoney');
                    return $gateway->process([
                        'order' => $random,
                        'txnid' => $random,
                        'amount' => $totalAmount,
                        'productinfo' => "New Transaction #" . $UserRequest->booking_id,
                        'firstname' => Auth::user()->first_name,
                        'email' => Auth::user()->email,
                        'phone' => Auth::user()->mobile,
                    ]);

                    break;

                  case 'PAYPAL':

                    $gateway = new PaymentGateway('paypal');
                    return $gateway->process([
                        'order' => $random,
                        'item_name' => $random,
                        'item_currency' => config('constants.paypal_currency'),
                        'item_quantity' => 1,
                        'amount' => $totalAmount,
                        'description' => 'Test',
                    ]);

                    break;

                  case 'PAYPAL-ADAPTIVE':

                    $gateway = new PaymentGateway('paypal-adaptive');

                    $provider = Provider::find($UserRequest->provider_id);

                    $provider_amount = 10;

                    if ($provider->paypal_email != null) {

                        $primary_email = config('constants.paypal_email', '');
                        $secondary_email[] = ['secondary_email' => $provider->paypal_email, 'amount' => $provider_amount];

                        return $gateway->process([
                            'order' => $random,
                            'primary_email' => $primary_email,
                            'secondary_email' => $secondary_email,
                            'amount' => $totalAmount,
                            'payer' => "EACHRECEIVER",
                        ]);

                    } else {
                        return redirect('dashboard')->with('flash_error', 'Please choose another payment method!');
                    }

                    break;

                  case 'PAYTM':

                    if ($request->ajax()) {

                        $callback_url = (config('constants.paytm_environment') == 'local') ? 'https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=' . $random : 'https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=' . $random;

                        $paramList["MID"] = config('constants.paytm_merchant_id');
                        $paramList["ORDER_ID"] = $random;
                        $paramList["CUST_ID"] = (String) Auth::user()->id;
                        $paramList["INDUSTRY_TYPE_ID"] = config('constants.paytm_industry_type');
                        $paramList["CHANNEL_ID"] = "WAP";
                        $paramList["TXN_AMOUNT"] = (double) $totalAmount;

                        $paramList["WEBSITE"] = 'APPSTAGING';
                        $paramList["CALLBACK_URL"] = $callback_url;
                        $paramList["MOBILE_NO"] = Auth::user()->mobile;
                        $paramList["EMAIL"] = Auth::user()->email;
                        $paramList["CHECKSUMHASH"] = PaytmLibrary::getChecksumFromArray($paramList, config('constants.paytm_merchant_key'));

                        return response()->json($paramList, 200);

                    }

                    $gateway = new PaymentGateway('paytm');

                    return $gateway->process([
                        'order' => $random,
                        'user' => Auth::user()->first_name,
                        'mobile_number' => Auth::user()->mobile,
                        'email' => Auth::user()->email,
                        'amount' => $totalAmount,
                        'callback_url' => url('/paytm/response'),
                    ]);

                    break;

                }

            }

        }
    }

    
    public function provide_cardsave(Request $request)
    {
        $card_id = $request->card_id;
        $last_four = $request->last4;
        $auth_code = $request->auth_code;
        $card_name = $request->card_name;                        
        $exist = ProviderCard::where('user_id',Auth::guard('provider')->user()->id)
                        ->where('brand',$card_name)
                        ->where('last_four',$last_four)
                        ->count();

        if($exist == 0){

            $create_card = new ProviderCard;
            $create_card->user_id = Auth::guard('provider')->user()->id;
            $create_card->card_id = $card_id;
            $create_card->last_four = $last_four;
            $create_card->auth_code = $auth_code;
            $create_card->brand = $card_name;
            $create_card->save();
            echo 'ok';
            exit;
        }else{
            if($request->ajax()){
                echo trans('api.card_already');
                exit;
            }     
        }
        echo "error";
    }



    public function cardsave(Request $request)
    {
        $card_id = $request->card_id;
        $last4 = $request->last4;
        $auth_code = $request->auth_code;
        $card_name = $request->card_name;                        
        $exist = PaystackCard::where('user_id',Auth::user()->id)
                        ->where('card_name',$card_name)
                        ->where('last4',$last4)
                        ->count();

        if($exist == 0){

            $create_card = new PaystackCard;
            $create_card->user_id = Auth::user()->id;
            $create_card->card_id = $card_id;
            $create_card->last4 = $last4;
            $create_card->auth_code = $auth_code;
            $create_card->card_name = $card_name;
            $create_card->save();
            echo 'ok';
            exit;
        }else{
            if($request->ajax()){
                echo trans('api.card_already');
                exit;
            }     
        }
        echo "error";
    }

    /**
     * add wallet money for user.
     *
     * @return \Illuminate\Http\Response
     */
    public function add_money(Request $request)
    {
        $random = config('constants.booking_prefix').mt_rand(100000, 999999);

        $user_type = $request->user_type;

        $log = new PaymentLog();
        $log->user_type = $user_type;
        $log->is_wallet = '1';
        $log->amount = $request->amount;
        $log->transaction_code = $random;
        $log->payment_mode = strtoupper($request->payment_mode);
        $log->user_id = \Auth::user()->id;
        $log->save();
        switch (strtoupper($request->payment_mode)) {

          case 'BRAINTREE':

           $gateway = new PaymentGateway('braintree');
            return $gateway->process([
                'amount' => $request->amount,
                'nonce' => $request->braintree_nonce,
                'order' => $random,
            ]);

            break;

          case 'CARD':

            if ($user_type == 'provider') 
            {

                //$Card = ProviderCard::where('user_id', $request->card_id)->first();
                if(config('constants.paystack') == 1)
                {
                    ProviderCard::where('user_id', Auth::user()->id)->update(['is_default' => 0]);
                    ProviderCard::where('card_id', $request->card_id)->update(['is_default' => 1]);
                } else if(config('constants.card') == 1)
                {
                    ProviderStripeCard::where('user_id', Auth::user()->id)->update(['is_default' => 0]);
                    ProviderStripeCard::where('card_id', $request->card_id)->update(['is_default' => 1]);
                }
            } else 
            {
                if(config('constants.card') == 1)
                {
                    Card::where('user_id', Auth::user()->id)->update(['is_default' => 0]);
                    Card::where('card_id', $request->card_id)->update(['is_default' => 1]);
                } else if(config('constants.paystack') == 1)
                {
                    PaystackCard::where('user_id', Auth::user()->id)->update(['is_default' => 0]);
                    PaystackCard::where('card_id', $request->card_id)->update(['is_default' => 1]);
                }
            }

                //$Card = Card::where('user_id', $request->card_id)->first();
                if(config('constants.card') == 1)
                {
                    $gateway = new PaymentGateway('stripe');
                    return $gateway->process([
                        "order" => $random,
                        "amount" => $request->amount,
                        "currency" => config('constants.stripe_currency'),
                        "customer" => Auth::user()->stripe_cust_id, 
                        "card" => $request->card_id,
                        "description" => "Adding Money for " . Auth::user()->email,
                        "receipt_email" => Auth::user()->email,
                        "type" => ($user_type == 'provider') ? 'connected_account' : '',
                    ]);

                } else if(config('constants.paystack') == 1)
                {
                    //PayStack Card Payment.

                    $gateway = new PaymentGateway('paystack');

                    return $gateway->process([
                        "order" => $random,
                        "amount" => $request->amount,
                        "currency" => config('constants.paystack_currency'),
                        "customer" =>  Auth::user()->id, 
                        "card" => $request->card_id,
                        "description" => "Adding Money for " . Auth::user()->email,
                        "receipt_email" => Auth::user()->email,
                        "type" => ($user_type == 'provider') ? 'connected_account' : '',
                    ]);

                }

            break;
            
          case 'PAYUMONEY':

            if ($request->ajax()) {

                $paramList = [
                    'key' => config('constants.payumoney_key'),
                    'txnid' => $random,
                    'amount' => $request->amount,
                    'productinfo' => "Wallet",
                    'firstname' => Auth::user()->first_name,
                    'email' => Auth::user()->email,
                    'phone' => Auth::user()->mobile,
                ];

                $paramList['surl'] = url('api/user/payu/response');
                $paramList['curl'] = url('api/user/payu/failure');
                $paramList['service_provider'] = 'payumoney';
                $paramList['merchant_id'] = config('constants.payumoney_merchant_id');
                $paramList['payu_salt'] = config('constants.payumoney_salt');

                $hash = '';
                // Hash Sequence
                $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";

                $hashVarsSeq = explode('|', $hashSequence);
                $hash_string = '';
                foreach ($hashVarsSeq as $hash_var) {
                    $hash_string .= isset($paramList[$hash_var]) ? $paramList[$hash_var] : '';
                    $hash_string .= '|';
                }

                $hash_string .= config('constants.payumoney_salt');

                $paramList['hash_string'] = $hash_string;
                $paramList['hash'] = hash('sha512', $hash_string);

                return response()->json($paramList, 200);

            }

            $gateway = new PaymentGateway('payumoney');
            return $gateway->process([
                'order' => $random,
                'txnid' => $random,
                'amount' => $request->amount,
                //Alias is used to trck the transaction, if it is failed we can remove that entry
                'productinfo' => 'Wallet',
                'firstname' => Auth::user()->first_name, # Payee Name.
                'email' => Auth::user()->email, # Payee Email Address.
                'phone' => Auth::user()->mobile, # Payee Phone Number.
            ]);

            break;
            
          case 'PAYPAL':

            $gateway = new PaymentGateway('paypal');
            return $gateway->process([
                'order' => $random,
                'item_name' => 'Item',
                'item_currency' => config('constants.paypal_currency'),
                'item_quantity' => 1,
                'amount' => $request->amount,
                'description' => 'Wallet Money added',
            ]);

            break;
            
          case 'PAYTM':

            if ($request->ajax()) {

                $callback_url = (config('constants.paytm_environment') == 'local') ? 'https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=' . $random : 'https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=' . $random;

                $paramList["MID"] = config('constants.paytm_merchant_id');
                $paramList["ORDER_ID"] = $random;
                $paramList["CUST_ID"] = (String) Auth::user()->id;
                $paramList["INDUSTRY_TYPE_ID"] = config('constants.paytm_industry_type');
                $paramList["CHANNEL_ID"] = "WAP";
                $paramList["TXN_AMOUNT"] = (double) $request->amount;

                $paramList["WEBSITE"] = 'APPSTAGING';
                $paramList["CALLBACK_URL"] = $callback_url;
                $paramList["MOBILE_NO"] = Auth::user()->mobile;
                $paramList["EMAIL"] = Auth::user()->email;
                $paramList["CHECKSUMHASH"] = PaytmLibrary::getChecksumFromArray($paramList, config('constants.paytm_merchant_key'));

                return response()->json($paramList, 200);

            }

            $gateway = new PaymentGateway('paytm');

            $provider_url = '';

            if ($request->type == 'provider') {
                $provider_url = '/provider';
            }

            return $gateway->process([
                'order' => $random,
                'user' => Auth::user()->first_name,
                'mobile_number' => Auth::user()->mobile,
                'email' => Auth::user()->email,
                'amount' => $request->amount,
                'callback_url' => url($provider_url . '/paytm/response'),
            ]);

            break;
        }

    }

    /**
     * send money to provider or fleet.
     *
     * @return \Illuminate\Http\Response
     */
    public function send_money(Request $request, $id)
    {

        try {

            $Requests = WalletRequests::where('id', $id)->first();

            if ($Requests->request_from == 'provider') {
                $provider = Provider::find($Requests->from_id);
                $stripe_acc_id = $provider->stripe_acc_id;
                $email = $provider->email;
            } else {
                $fleet = Fleet::find($Requests->from_id);
                $stripe_acc_id = $fleet->stripe_acc_id;
                $email = $fleet->email;
            }

            if (empty($stripe_acc_id)) {
                throw new Exception(trans('admin.payment_msgs.account_not_found'));
            }

            $StripeCharge = $Requests->amount;

            Stripe::setApiKey(config('constants.stripe_secret_key'));

            $tranfer = \Stripe\Transfer::create(array(
                "amount" => $StripeCharge,
                "currency" => "usd",
                "destination" => $stripe_acc_id,
                "description" => "Payment Settlement for " . $email,
            ));

            //create the settlement transactions
            (new TripController)->settlements($id);

            $response = array();
            $response['success'] = trans('admin.payment_msgs.amount_send');

        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
        }

        return $response;
    }

    public function handleGatewayCallback()
    {
         $paymentDetails = Paystack::getPaymentData();

         
        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want
    }

    public function redirectToGateway()
    {
        return Paystack::getAuthorizationUrl()->redirectNow();
    }

    public function getPayStackAccessCode()
    {
        echo Paystack::getAccessCode();
    }

    public function paystack_verify()
    {
        $result = Paystack::getVerifyResponse();
        $returnval = [];
        if($result['status'])
        {
            $returnval['status'] = 'success';
            $returnval['response'] = $result['data'];
            echo json_encode($returnval);
        } else
        {
            $returnval['status'] = 'failed';
            $returnval['response'] = '';
            echo json_encode($returnval);
        }
    }

    public function response(Request $request)
    {

        $log = PaymentLog::where('transaction_code', $request->order)->first();

        if($log->is_wallet == 1) {

            if ($log->user_type == "user") {
                $user = \App\User::find($log->user_id);
                $wallet = (new TripController)->userCreditDebit($log->amount, $user->id, 1);
                (new SendPushNotification)->WalletMoney($user->id, currency($log->amount));
            } else if ($log->user_type == "provider") {
                $user = \App\Provider::find($log->user_id);
                $wallet = (new TripController)->providerCreditDebit($log->amount, $user->id, 1);
                (new SendPushNotification)->ProviderWalletMoney($user->id, currency($log->amount));
            }

            $wallet_balance = $user->wallet_balance+$log->amount;

            if ($request->ajax()) {
                return response()->json(['success' => currency($log->amount) . " " . trans('api.added_to_your_wallet'), 'message' => currency($log->amount) . " " . trans('api.added_to_your_wallet'), 'wallet_balance' => $wallet_balance]);
            } else {
                if ($log->user_type == "provider") {
                    return redirect('/provider/wallet_transation')->with('flash_success', currency($log->amount) . trans('admin.payment_msgs.amount_added'));
                } else {
                    return redirect('wallet')->with('flash_success', currency($log->amount) . trans('admin.payment_msgs.amount_added'));
                }

            }

        }


        $payment_id = $request->has('pay') ? $request->pay : null;

        switch ($log->payment_mode) {

          case 'BRAINTREE':
            # code...
            break;
          case 'CARD':
            # code...
            break;
          case 'PAYUMONEY':
            # code...
            break;
          
          
          case 'PAYPAL-ADAPTIVE':

            break;


          case 'PAYPAL':

            $paypal_conf = \Config::get('paypal');
            $api_context = new ApiContext(new OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret'])
            );
            $api_context->setConfig($paypal_conf['settings']);

            $payment = Payment::get($request->paymentId, $api_context);

            $execution = new PaymentExecution();
            $execution->setPayerId($request->PayerID);

            //Execute the payment
            $result = $payment->execute($execution, $api_context);
            $log->response = $result;
            $log->save();

            if ($result->getState() == 'approved') {
                $payment_id = $request->PayerID;
            }

            break;

          
          case 'PAYTM':
            # code...
            break;
        }

        $UserRequest = UserRequests::find($log->transaction_id);

        $RequestPayment = UserRequestPayment::where('request_id', $UserRequest->id)->first();
        $RequestPayment->payment_id = $payment_id;
        $RequestPayment->payment_mode = $UserRequest->payment_mode;
        $RequestPayment->card = $RequestPayment->payable;
        $RequestPayment->save();

        $UserRequest->paid = 1;
        $UserRequest->status = 'COMPLETED';
        $UserRequest->save();

        //for create the transaction
        (new TripController)->callTransaction($UserRequest->id);

        if ($request->ajax()) {
            return response()->json(['message' => trans('api.paid')]);
        } else {
            return redirect('dashboard')->with('flash_success', trans('api.paid'));
        }

    }

    public function failure(Request $request)
    {
        $log = PaymentLog::where('transaction_code', $request->order)->first();

        if($log->is_wallet == 1) {

            if ($request->ajax()) {
                return response()->json(['success' => 'false', 'message' => 'Transaction Failed']);
            } else {
                if ($log->user_type == "provider") {
                    return redirect('/provider/wallet_transation')->with('flash_error', 'Transaction Failed');
                } else {
                    return redirect('wallet')->with('flash_error', 'Transaction Failed');
                }
            }

        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Transaction Failed']);
        } else {
            if ($log->user_type == "provider") {
                return redirect('/')->with('flash_success', 'Transaction Failed');
            } else {
                return redirect('dashboard')->with('flash_success', 'Transaction Failed');
            }

        }

    }

    public function paytm_response(Request $request)
    {

        $log = PaymentLog::where('transaction_code', $request->ORDERID)->first();
        $log->response = $request->all();
        $log->save();

        $provider_url = $log->user_type == 'provider' ? '/provider' : '' ;

        if ($request->STATUS == "TXN_SUCCESS") {
            return redirect($provider_url . '/payment/response?order='. $request->ORDERID. '&pay=' . $request->TXNID );
        } else {
            return redirect($provider_url . '/payment/failure?order='. $request->ORDERID );
        }

    }

    public function payu_response(Request $request)
    {
        $log = PaymentLog::where('transaction_code', $request['txnid'])->first();
        $log->response = json_encode($request->all());
        $log->save();

        $provider_url = $log->user_type == 'provider' ? '/provider' : '' ;

        return redirect($provider_url . '/payment/response?order='. $request['txnid']. '&pay=' . $request->payuMoneyId );

    }

    public function payu_error(Request $request)
    {

        $log = PaymentLog::where('transaction_code', $request)->first();
        $log->response = json_encode($request);
        $log->save();

        $provider_url = $log->user_type == 'provider' ? '/provider' : '' ;

        return redirect($provider_url . '/payment/failure?order='. $request['txnid'] );
    }

}
