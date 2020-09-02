<?php 

namespace App\Services;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Exception;
use DateTime;
use Auth;
use Lang;
use Setting;
use App\ServiceType;
use App\Promocode;
use App\Provider;
use App\ProviderService;
use App\Helpers\Helper;
use GuzzleHttp\Client;
use App\PaymentLog;


//PayuMoney
use Tzsk\Payu\Facade\Payment AS PayuPayment;

//Paypal
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payee;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Paystack;
use App\PaystackCard;
use App\ProviderCard;

use Redirect;
use Session;
use URL;


class PaymentGateway {

	private $gateway;

	public function __construct($gateway){
		$this->gateway = strtoupper($gateway);
	}

	public function process($attributes) {

		$provider_url = '';

		$gateway = ($this->gateway == 'STRIPE' || $this->gateway == 'PAYSTACK') ? 'CARD' : $this->gateway ;

		$log = PaymentLog::where('transaction_code', $attributes['order'])->where('payment_mode', $gateway )->first();


		if($log->user_type == 'provider') {
			$provider_url = '/provider';
		}

		switch ($this->gateway) {

			case "BRAINTREE":

				(new \App\Http\Controllers\UserApiController())->set_Braintree();
				$result = \Braintree_Transaction::sale([
                  'amount' => $attributes['amount'],
                  'paymentMethodNonce' => $attributes['nonce'],
                  'orderId' => $attributes['order'],
                  'options' => [
                      'submitForSettlement' => True,
                      'paypal' => [
                    ],
                  ]
                ]);

                $log->response = $result->transaction;
                $log->save();

                if($result->success == true) {
					return redirect($provider_url.'/payment/response?order='. $attributes['order'] .'&pay='. $result->transaction->id);
				} else {
					return redirect($provider_url.'/payment/failure?order='. $attributes['order']);
				}
				
				break;

			 case "PAYSTACK":

			 	 
			 	
			 		if($log->user_type == 'provider'){
			 			$ClientCard = ProviderCard::where('card_id', $attributes['card'])->first();
			 		} else {
			 			$ClientCard = PaystackCard::where('card_id', $attributes['card'])->first();
			 		}

			 		$auth_code = $ClientCard->auth_code;
			 		$body = ['amount' => $attributes['amount'] * 100,
  							 'email' => Auth::user()->email,
  							'authorization_code' => $auth_code];

  					$Charge = Paystack::chargePayment('/transaction/charge_authorization', $body);
		            $log->response = json_encode($Charge);
		            $log->save();


                	if($Charge['status'] == 'success')
                	{
                		return redirect($provider_url.'/payment/response?order='. $attributes['order'].'&pay='.$Charge['reference']);
    				} else
    				{
    					return redirect($provider_url.'/payment/failure?order='. $attributes['order']);	
    				}
			 	
			 break;

			case "STRIPE":
				try {
					\Stripe\Stripe::setApiKey(config('constants.stripe_secret_key', ''));

					if( !empty($attributes['type']) && $attributes['type'] == "connected_account") {
						$Charge = \Stripe\Charge::create([
	                    "amount" => $attributes['amount'] * 100,
	                    "currency" => $attributes['currency'],
	                    "customer" => $attributes['customer'],
	                    "card" => $attributes['card'],
	                    "description" => $attributes['description'],
	                    "receipt_email" => $attributes['receipt_email']
	                  ]);
					} else {
						// $Charge = \Stripe\Charge::create([
	     //                "amount" => $attributes['amount'] * 100,
	     //                "currency" => $attributes['currency'],
	     //                "customer" => $attributes['customer'],
	     //                "card" => $attributes['card'],
	     //                "description" => $attributes['description'],
	     //                "receipt_email" => $attributes['receipt_email']
	     //              ]);


						$total_amount = $attributes['amount'] * 100;
						$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					    $length = 10;
					    $charactersLength = strlen($characters);
					    $randomString = '';
					    for ($i = 0; $i < $length; $i++) {
					        $randomString .= $characters[rand(0, $charactersLength - 1)];
					    }
						$group_id = $randomString;

						 $Balance = \Stripe\Balance::retrieve();
						 var_dump($Balance);exit;

						$Charge = \Stripe\Charge::create([
							  'amount' => $total_amount,
							  'currency' => 'usd',
							  'transfer_group' => $group_id,
							  "customer" => $attributes['customer'],
			                  "card" => $attributes['card'],
			                  "description" => $attributes['description'],
			                  "receipt_email" => $attributes['receipt_email']
							]);


						
						
						//Driver
						//$driver_stripe_id = "acct_1FjziFF8Q7M9tWyS";
						$driver_stripe_id = "acct_1FjspyFroL1d1l6Z";
						

						$Charge1 = \Stripe\Transfer::create([
							'amount' => 100,
  							'currency' => 'usd',
  							'destination' => $driver_stripe_id
						]);

						//escrow
						// $escrow_stripe_id = "acct_1FjstlJiixsamYlX";
						// $Charge2 = \Stripe\Transfer::create([
						// 	'amount' => floor($total_amount * 0.1),
  				// 			'currency' => 'usd',
  				// 			'destination' => $escrow_stripe_id,
  				// 			'transfer_group' => $group_id,
						// ]);

						// //Shareholders
						// $shareholders_stripe_id = "acct_1FjswFGtNxgLl8qy";
						// $Charge3 = \Stripe\Transfer::create([
						// 	'amount' => floor($total_amount * 0.1),
  				// 			'currency' => 'usd',
  				// 			'destination' => $shareholders_stripe_id,
  				// 			'transfer_group' => $group_id,
						// ]);

						// //INS
						// $ins_stripe_id = "acct_1FjsyMLBv9xrmGJm";
						// $Charge4 = \Stripe\Transfer::create([
						// 	'amount' => floor($total_amount * 0.1),
  				// 			'currency' => 'usd',
  				// 			'destination' => $ins_stripe_id,
  				// 			'transfer_group' => $group_id,
						// ]);

						// //Other
						// $other_stripe_id = "acct_1FjsHzEOmDsvqyFd";
						// $Charge5 = \Stripe\Transfer::create([
						// 	'amount' => floor($total_amount * 0.15),
  				// 			'currency' => 'usd',
  				// 			'destination' => $other_stripe_id,
  				// 			'transfer_group' => $group_id,
						// ]);

					}

					
					$log->response = json_encode($Charge);
                	$log->save();

					$paymentId = $Charge['id'];

					return redirect($provider_url.'/payment/response?order='. $attributes['order'].'&pay='.$paymentId);

				} catch(StripeInvalidRequestError $e){
	                //var_dump($e);exit;
	                return redirect($provider_url.'/payment/failure?order='. $attributes['order']);

	            } catch(Exception $e) {
	            	//var_dump($e);exit;
	                return redirect($provider_url.'/payment/failure?order='. $attributes['order']);
	            }

				break;

			case "PAYUMONEY":

				return PayuPayment::make([
                      'txnid' => $attributes['txnid'],
                      'amount' => $attributes['amount'],
                      'productinfo' => $attributes['productinfo'],
                      'firstname' => $attributes['firstname'], # Payee Name.
                      'email' => $attributes['email'], # Payee Email Address.
                      'phone' => $attributes['phone'], # Payee Phone Number.
                  ], function ($then) use($attributes, $provider_url) {

					$url = '/payu/response?order='. $attributes['order'];

					$then->redirectTo($url);
				});

				break;

			case "PAYPAL":

				$paypal_conf = \Config::get('paypal');
				$this->_api_context = new ApiContext(new OAuthTokenCredential(
		            $paypal_conf['client_id'],
		            $paypal_conf['secret'])
		        );
		        $this->_api_context->setConfig($paypal_conf['settings']);

				$payer = new Payer();
				$payer->setPaymentMethod('paypal');

				$item1 = new Item(); 
				$item1->setName($attributes['item_name'])->setCurrency($attributes['item_currency'])->setQuantity($attributes['item_quantity'])->setPrice($attributes['amount']); 
				$itemList = new ItemList(); 
				$itemList->setItems(array($item1));

				$amount = new Amount();
				$amount->setCurrency($attributes['item_currency'])
					->setTotal($attributes['amount']);

				$transaction = new Transaction();
				$transaction->setAmount($amount)
					->setItemList($itemList)
					->setDescription($attributes['description']);

				$redirect_urls = new RedirectUrls();
				$redirect_urls->setReturnUrl(URL::to($provider_url.'/payment/response?order='. $attributes['order']))
				->setCancelUrl(URL::to($provider_url.'/payment/failure?order='. $attributes['order']));

				$payment = new Payment();
				$payment->setIntent('Sale')
					->setPayer($payer)
					->setRedirectUrls($redirect_urls)
					->setTransactions(array($transaction));

				try {
					$payment->create($this->_api_context);
				} catch (\PayPal\Exception\PPConnectionException $ex) {
					if (\Config::get('app.debug')) {
						\Session::put('error', 'Connection timeout');
						return Redirect::to('/');
					} else {
						\Session::put('error', 'Some error occur, sorry for inconvenient');
						return Redirect::to('/');
					}
				}
				
				foreach ($payment->getLinks() as $link) {
					if ($link->getRel() == 'approval_url') {
						$redirect_url = $link->getHref();
						break;
					}
				}

				if (isset($redirect_url)) {
					return Redirect::away($redirect_url);
				}

				break;

			case "PAYPAL-ADAPTIVE":

				$payment = new \Srmklive\PayPal\Services\AdaptivePayments;

				$data = [];

				$data['receivers'][] = ['email' => $attributes['primary_email'], 'amount' => $attributes['amount'], 'primary' => true];

				foreach ($attributes['secondary_email'] as $secondary_email) {
					$data['receivers'][] = ['email' => $secondary_email['secondary_email'], 'amount' => $secondary_email['amount'], 'primary' => false];
				}

                // (Optional) Describes who pays PayPal fees. Allowed values are: 'SENDER', 'PRIMARYRECEIVER', 'EACHRECEIVER' (Default), 'SECONDARYONLY'
				$data['payer'] = $attributes['payer'];
				$data['return_url'] = url($provider_url.'/payment/response?order='. $attributes['order']);
				$data['cancel_url'] = url($provider_url.'/payment/failure?order='. $attributes['order']);


				try {
					$response = $payment->createPayRequest($data);
					$redirect_url = $payment->getRedirectUrl('approved', $response['payKey'], $attributes['order']);

					$log->response = json_encode($response);
                	$log->save();

					return redirect($redirect_url);

				} catch(\Throwable $e) {
					Session::flash('message', 'Payment Error Occured');
					return redirect('dashboard');
				}

				break;

			case "PAYTM":

			        $payment = \PaytmWallet::with('receive');

			        $payment->prepare([
			          'order' => $attributes['order'],
			          'user' => $attributes['user'],
			          'mobile_number' => $attributes['mobile_number'],
			          'email' => $attributes['email'],
			          'amount' => $attributes['amount'],
			          'callback_url' => $attributes['callback_url']
			        ]);

        		return $payment->receive();

				break;

			default:
				return redirect('dashboard');
		}
		

	}
	
}