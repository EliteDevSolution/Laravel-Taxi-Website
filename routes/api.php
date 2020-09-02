<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/settings' , 'UserApiController@settings');
Route::post('/verify' , 'UserApiController@verify');
Route::post('/checkemail' , 'UserApiController@checkUserEmail');

Route::post('/oauth/token' , 'UserApiController@login');
Route::post('/signup' , 'UserApiController@signup');
Route::post('/logout' , 'UserApiController@logout');
Route::get('/checkapi' , 'UserApiController@checkapi');
Route::post('/checkversion' , 'UserApiController@CheckVersion');


Route::post('/auth/facebook', 		'Auth\SocialLoginController@facebookViaAPI');
Route::post('/auth/google', 		'Auth\SocialLoginController@googleViaAPI');
Route::post('/forgot/password',     'UserApiController@forgot_password');
Route::post('/reset/password',      'UserApiController@reset_password');
Route::get('/estimated/fare_without_auth' , 'UserApiController@estimated_fare');

Route::group(['middleware' => ['auth:api']], function () {

	// estimated
	Route::get('/estimated/fare' , 'UserApiController@estimated_fare');

	// user profile
	Route::post('/change/password' , 	'UserApiController@change_password');
	Route::post('/update/location' , 	'UserApiController@update_location');
	Route::post('/update/language' , 	'UserApiController@update_language');
	Route::get('/details' , 			'UserApiController@details');
	Route::post('/update/profile' , 	'UserApiController@update_profile');
	// services
	Route::get('/services' , 'UserApiController@services');
	// provider
	Route::post('/rate/provider' , 'UserApiController@rate_provider');

	// request
	Route::post('/send/request' , 	'UserApiController@send_request');
	Route::post('/cancel/request' , 'UserApiController@cancel_request');
	Route::get('/request/check' , 	'UserApiController@request_status_check');
	Route::get('/show/providers' , 	'UserApiController@show_providers');
	Route::post('/update/request' , 'UserApiController@modifiy_request');
	// history
	Route::get('/trips' , 				'UserApiController@trips');
	Route::get('upcoming/trips' , 		'UserApiController@upcoming_trips');
	Route::get('/trip/details' , 		'UserApiController@trip_details');
	Route::get('upcoming/trip/details' ,'UserApiController@upcoming_trip_details');
	Route::post('extend/trip' , 			'UserApiController@extend_trip');

	// Payment
	Route::post('/payment' , 	'PaymentController@payment');
	Route::post('/add/money' , 	'PaymentController@add_money');

	// Braintree
	Route::get('/braintree/token' , 'UserApiController@client_token');

	// Payu
	Route::post('/payu/response', 'PaymentController@payu_response');
	Route::post('/payu/failure', 'PaymentController@payu_error');

	//paytm
	Route::post('/paytm/response', 'PaymentController@paytm_response');

	// Payment Success
	Route::get('/payment/response', 'PaymentController@response');

	// Payment Failure
	Route::get('/payment/failure', function () { return "failure"; });


	
	// help
	Route::get('/help' , 'UserApiController@help_details');
	// promocode
	Route::get('/promocodes_list','UserApiController@list_promocode');
	Route::get('/promocodes' , 		'UserApiController@promocodes');
	Route::post('/promocode/add' , 	'UserApiController@add_promocode');
	// card payment
    Route::resource('card', 		'Resource\CardResource');
    // card payment
    Route::resource('location', 'Resource\FavouriteLocationResource');
    // passbook
	Route::get('/wallet/passbook' , 'UserApiController@wallet_passbook');
	Route::get('/promo/passbook' , 	'UserApiController@promo_passbook');

	Route::post('/test/push' , 	'UserApiController@test');

	Route::post('/chat' , 'UserApiController@chatPush');

	Route::get('/reasons', 'UserApiController@reasons');

	Route::get('/notifications/{type}', 'Resource\NotificationResource@getnotify');

	Route::post('/dispute-list', 'Resource\DisputeResource@dispute_list');

	Route::post('/dispute', 'Resource\DisputeResource@create_dispute');

	Route::post('/drop-item', 'Resource\LostItemResource@saveLostItem');

	Route::patch('/drop-item/{id}', 'Resource\LostItemResource@update');

	Route::post('/payment-log', 'UserApiController@payment_log');

});

Route::post('/verify-credentials', 'UserApiController@verifyCredentials');

Route::post('/save-subscription/{id}', 'HomeController@save_subscription')->name('save_subscription');