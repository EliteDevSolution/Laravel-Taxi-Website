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

Route::get('/settings' , 'ProviderAuth\TokenController@settings');
// Authentication
Route::post('/register' ,   'ProviderAuth\TokenController@register');
Route::post('/oauth/token' ,'ProviderAuth\TokenController@authenticate');
Route::post('/logout' ,     'ProviderAuth\TokenController@logout');
Route::post('/verify' ,     'ProviderAuth\TokenController@verify');
Route::post('/auth/facebook','ProviderAuth\TokenController@facebookViaAPI');
Route::post('/auth/google',  'ProviderAuth\TokenController@googleViaAPI');
Route::post('/forgot/password','ProviderAuth\TokenController@forgot_password');
Route::post('/reset/password', 'ProviderAuth\TokenController@reset_password');

Route::get('/refresh/token' , 'ProviderAuth\TokenController@refresh_token');
Route::post('update-location','ProviderResources\TripController@track_location');
Route::get('get-location','ProviderResources\TripController@track_location_get');
Route::get('remove-location','ProviderResources\TripController@track_location_remove');

Route::group(['middleware' => ['provider.api']], function () {

    // Payment
    Route::post('/payment' ,    'PaymentController@payment');
    Route::post('/add/money' ,  'PaymentController@add_money');

    Route::post('/payment-log', 'UserApiController@payment_log');

    // Braintree
    Route::get('/braintree/token' , 'UserApiController@client_token');

    // Payu
    Route::post('/payu/response', 'PaymentController@payu_response');
    Route::post('/payu/failure', 'PaymentController@payu_error');

    // Payment Success
    Route::get('/payment/success', 'PaymentController@response');

    // Payment Failure
    Route::get('/payment/failure', function () { return "failure"; });

    //Route::post('/refresh/token' , 'ProviderAuth\TokenController@refresh_token');

    Route::group(['prefix' => 'profile'], function () {

        Route::get ('/' ,         'ProviderResources\ProfileController@index');
        Route::post('/' ,         'ProviderResources\ProfileController@update');
        Route::post('/password' , 'ProviderResources\ProfileController@password');
        Route::post('/location' , 'ProviderResources\ProfileController@location');
        Route::post('/language' , 'ProviderResources\ProfileController@update_language');
        Route::post('/available', 'ProviderResources\ProfileController@available');
        Route::get ('/documents', 'ProviderResources\ProfileController@documents');
        Route::post('/documents/store', 'ProviderResources\ProfileController@documentstore');       

    });

    Route::resource('providercard', 'Resource\ProviderCardResource');

    Route::post('/chat' , 'ProviderResources\ProfileController@chatPush');

    Route::get('/target' , 'ProviderResources\ProfileController@target');
    Route::resource('trip','ProviderResources\TripController');
    Route::post('cancel',  'ProviderResources\TripController@cancel');
    Route::post('summary', 'ProviderResources\TripController@summary');
    Route::get('help',     'ProviderResources\TripController@help_details');
    Route::get('/wallettransaction', 'ProviderResources\TripController@wallet_transation');
    Route::get('/wallettransaction/details', 'ProviderResources\TripController@wallet_details');
    Route::get('/transferlist', 'ProviderResources\TripController@transferlist');
    Route::post('/requestamount' ,'ProviderResources\TripController@requestamount');
    Route::get('/requestcancel' ,'ProviderResources\TripController@requestcancel');
    Route::post('/waiting', 'ProviderResources\TripController@waiting');
   


    Route::group(['prefix' => 'trip'], function () {

        Route::post('{id}',          'ProviderResources\TripController@accept');
        Route::post('{id}/rate',     'ProviderResources\TripController@rate');
        Route::post('{id}/message' , 'ProviderResources\TripController@message');
        Route::post('{id}/calculate','ProviderResources\TripController@calculate_distance');

    });
    
    Route::post('requests/rides' , 'ProviderResources\TripController@request_rides');

    Route::group(['prefix' => 'requests'], function () {
        Route::post('/instant/ride' ,   'ProviderResources\TripController@instant_ride');
        Route::get('/upcoming' ,       'ProviderResources\TripController@scheduled');
        Route::get('/history',         'ProviderResources\TripController@history');
        Route::get('/history/details', 'ProviderResources\TripController@history_details');
        Route::get('/upcoming/details','ProviderResources\TripController@upcoming_details');

    });

    Route::post('/test/push' ,  'ProviderResources\TripController@test');

    Route::get('/reasons', 'ProviderResources\ProfileController@reasons');

    Route::get('/notifications/{type}', 'Resource\NotificationResource@getnotify');

    Route::post('/dispute-list', 'Resource\DisputeResource@dispute_list');

    Route::post('/dispute', 'Resource\DisputeResource@create_dispute');

    Route::post('/drop-item', 'Resource\LostItemResource@store');

    

});

Route::post('/verify-credentials', 'ProviderResources\ProfileController@verifyCredentials');

