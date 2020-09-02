<?php

date_default_timezone_set(config('constants.timezone', 'UTC'));
/*
|--------------------------------------------------------------------------
| User Authentication Routes
|--------------------------------------------------------------------------
*/


Auth::routes();


Route::post('/contact/us', 'HomeController@contactus')->name('contact');

Route::get('auth/facebook', 'Auth\SocialLoginController@redirectToFaceBook');
Route::get('auth/facebook/callback', 'Auth\SocialLoginController@handleFacebookCallback');
Route::get('auth/google', 'Auth\SocialLoginController@redirectToGoogle');
Route::get('auth/google/callback', 'Auth\SocialLoginController@handleGoogleCallback');
Route::post('account/kit', 'Auth\SocialLoginController@account_kit')->name('account.kit');

/*
|--------------------------------------------------------------------------
| Provider Authentication Routes
|--------------------------------------------------------------------------
*/

Route::post('/provider/verify-credentials', 'ProviderResources\ProfileController@verifyCredentials');
Route::post('/user/verify-credentials', 'UserApiController@verifyCredentials');

Route::group(['prefix' => 'provider'], function () {

    Route::get('auth/facebook', 'Auth\SocialLoginController@providerToFaceBook');
    Route::get('auth/google', 'Auth\SocialLoginController@providerToGoogle');

    Route::get('/login', 'ProviderAuth\LoginController@showLoginForm');
    Route::post('/login', 'ProviderAuth\LoginController@login');
    Route::post('/logout', 'ProviderAuth\LoginController@logout');

    Route::get('/register', 'ProviderAuth\RegisterController@showRegistrationForm');
    Route::post('/register', 'ProviderAuth\RegisterController@register');

    Route::post('/password/email', 'ProviderAuth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'ProviderAuth\ResetPasswordController@reset');
    Route::get('/password/reset', 'ProviderAuth\ForgotPasswordController@showLinkRequestForm');
    Route::get('/password/reset/{token}', 'ProviderAuth\ResetPasswordController@showResetForm');
});

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'admin'], function () {
    Route::get('/login', 'AdminAuth\LoginController@showLoginForm');
    Route::post('/login', 'AdminAuth\LoginController@login');
    Route::post('/logout', 'AdminAuth\LoginController@logout');

    Route::post('/password/email', 'AdminAuth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'AdminAuth\ResetPasswordController@reset');
    Route::get('/password/reset', 'AdminAuth\ForgotPasswordController@showLinkRequestForm');
    Route::get('/password/reset/{token}', 'AdminAuth\ResetPasswordController@showResetForm');
});

/*
|--------------------------------------------------------------------------
| Dispatcher Authentication Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'dispatcher'], function () {
    Route::get('/login', 'DispatcherAuth\LoginController@showLoginForm');
    Route::post('/login', 'DispatcherAuth\LoginController@login');
    Route::post('/logout', 'DispatcherAuth\LoginController@logout');

    Route::post('/password/email', 'DispatcherAuth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'DispatcherAuth\ResetPasswordController@reset');
    Route::get('/password/reset', 'DispatcherAuth\ForgotPasswordController@showLinkRequestForm');
    Route::get('/password/reset/{token}', 'DispatcherAuth\ResetPasswordController@showResetForm');
});

/*
|--------------------------------------------------------------------------
| Fleet Authentication Routes
|--------------------------------------------------------------------------
*/


Route::group(['prefix' => 'fleet'], function () {
    Route::get('/login', 'FleetAuth\LoginController@showLoginForm');
    Route::post('/login', 'FleetAuth\LoginController@login');
    Route::post('/logout', 'FleetAuth\LoginController@logout');

    Route::post('/password/email', 'FleetAuth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'FleetAuth\ResetPasswordController@reset');
    Route::get('/password/reset', 'FleetAuth\ForgotPasswordController@showLinkRequestForm');
    Route::get('/password/reset/{token}', 'FleetAuth\ResetPasswordController@showResetForm');
});

/*
|--------------------------------------------------------------------------
| Account Authentication Routes
|--------------------------------------------------------------------------
*/


Route::group(['prefix' => 'account'], function () {
    Route::get('/login', 'AccountAuth\LoginController@showLoginForm');
    Route::post('/login', 'AccountAuth\LoginController@login');
    Route::post('/logout', 'AccountAuth\LoginController@logout');

    Route::get('/register', 'AccountAuth\RegisterController@showRegistrationForm');
    Route::post('/register', 'AccountAuth\RegisterController@register');

    Route::post('/password/email', 'AccountAuth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'AccountAuth\ResetPasswordController@reset');
    Route::get('/password/reset', 'AccountAuth\ForgotPasswordController@showLinkRequestForm');
    Route::get('/password/reset/{token}', 'AccountAuth\ResetPasswordController@showResetForm');
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', 'WelcomeController@index');

Route::get('/initsetup', function () {
    return Setting::all();
});

Route::get('/ride', function () {
    return view('ride');
});

Route::get('/noaccess', function () {
    return view('noaccess');
});


Route::get('/ride', 'Auth\RegisterController@ride');
Route::get('/sendmail', 'SendMailController@index');
Route::post('/sendmail/verify', 'SendMailController@verify')->name('verify');
Route::post('/sendmail/createusers', 'SendMailController@createusers')->name('createusers');
Route::get('/sendmail/form', 'SendMailController@showmailform')->name('showmailform');

Route::get('/drive', function () {
    return view('drive');
});

Route::get('privacy', function () {
    $page = 'page_privacy';
    $title = 'Privacy Policy';
    return view('static', compact('page', 'title'));
});

Route::get('terms', function () {
    $page = 'terms';
    $title = 'Terms and Conditions';
    return view('static', compact('page', 'title'));
});
Route::get('cancellation', function () {
    $page = 'cancel';
    $title = 'Cancellation Rules';
    return view('static', compact('page', 'title'));
});

Route::get('help', function () {
    $page = 'help';
    $title = 'Help';
    return view('static', compact('page', 'title'));
});


/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', 'HomeController@index');
Route::get('/hour/{id}', 'UserApiController@pricing_logic');
// user profiles
Route::get('/profile', 'HomeController@profile');
Route::get('/edit/profile', 'HomeController@edit_profile');
Route::post('/profile', 'HomeController@update_profile');

// update password
Route::get('/change/password', 'HomeController@change_password');
Route::post('/change/password', 'HomeController@update_password');

// ride
Route::get('/confirm/ride', 'RideController@confirm_ride');
Route::post('/create/ride', 'RideController@create_ride');
Route::post('/cancel/ride', 'RideController@cancel_ride');
Route::get('/onride', 'RideController@onride');
Route::post('/payment', 'PaymentController@payment');


Route::post('/rate', 'RideController@rate');
Route::get('/referral', 'HomeController@referral');
Route::post('/extend/trip', 'UserApiController@extend_trip');
// status check
Route::get('/status', 'RideController@status');
Route::get('/user/incoming', 'HomeController@incoming');


// trips
Route::get('/trips', 'HomeController@trips');
//notifications
Route::get('/notifications', 'HomeController@notifications');
//Lost Item
Route::get('/lostitem/{id}', 'HomeController@lostitem');
Route::post('/lostitem', 'HomeController@lostitem_store');
//Dispute
Route::get('/dispute/{id}', 'HomeController@dispute');
Route::post('/dispute', 'HomeController@dispute_store');

Route::get('/upcoming/trips', 'HomeController@upcoming_trips');



// wallet
Route::get('/wallet', 'HomeController@wallet');
Route::post('/add/money', 'PaymentController@add_money');

// payment
Route::get('/payment', 'HomeController@payment');

// card
Route::resource('card', 'Resource\CardResource');


// promotions
Route::get('/promotions', 'HomeController@promotions_index')->name('promocodes.index');
Route::post('/promotions', 'HomeController@promotions_store')->name('promocodes.store');

Route::post('/fare' , 'UserApiController@fare');

Route::post('/verify-credentials', 'UserApiController@verifyCredentials');

Route::get('/payment/response', 'PaymentController@response')->name('payment.success');

Route::get('/payment/failure', 'PaymentController@failure')->name('payment.failure');
Route::post('/card/cardsave','PaymentController@cardsave')->name('card.cardsave');
Route::post('/card/provide_cardsave','PaymentController@provide_cardsave')->name('card.provide_cardsave');


//Paystack
Route::get('/paystack/callback', 'PaymentController@handleGatewayCallback');
Route::get('/paystackverify', 'PaymentController@paystack_verify')->name('paystackverify');
Route::post('/paystack/response', 'PaymentController@paystack_response');
Route::post('/paystackaccess', 'PaymentController@getPayStackAccessCode')->name('paystackaccess');
Route::post('/paystack', 'PaymentController@redirectToGateway')->name('paystack');







//paytm
Route::post('/paytm/response', 'PaymentController@paytm_response');

//Payu
Route::post('/payu/response', 'PaymentController@payu_response');
Route::post('/payu/failure', 'PaymentController@payu_error');

Route::get('/track/{id}', 'HomeController@track')->name('track');

Route::post('/track', 'HomeController@track_location')->name('track');

// send push notification
Route::get('/send/ios/push',
    function(){
        $data = PushNotification::app('IOSUser')
        ->to('163e4c0ca9fe084aabeb89372cf3f664790ffc660c8b97260004478aec61212c')
        ->send('Hello World, i`m a push message');
            dd($data);
    });


Route::get('/send/android/push',
    function(){
        $data = PushNotification::app('Android')
        ->to('163e4c0ca9fe084aabeb89372cf3f664790ffc660c8b97260004478aec61212c')
        ->send('Hello World, i`m a push message');
            dd($data);
    });