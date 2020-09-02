<?php 

return [
    'mode'    => config('constants.paypal_adaptive_environment', ''), // Can only be 'sandbox' Or 'live'. If empty or invalid, 'live' will be used.
    'sandbox' => [
        'username'    => config('constants.paypal_username', ''),
        'password'    => config('constants.paypal_password', ''),
        'secret'      => config('constants.paypal_secret', ''),
        'certificate' => config('constants.paypal_certificate', ''),
        'app_id'      => config('constants.paypal_app_id', ''), 
    ],
    'live' => [
        'username'    => config('constants.paypal_username', ''),
        'password'    => config('constants.paypal_password', ''),
        'secret'      => config('constants.paypal_secret', ''),
        'certificate' => config('constants.paypal_certificate', ''),
        'app_id'      => config('constants.paypal_app_id', ''), 
    ],

    'payment_action' => 'Sale', // Can only be 'Sale', 'Authorization' or 'Order'
    'currency'       => config('constants.paypal_adaptive_currency', ''),
    'notify_url'     => '', // Change this accordingly for your application.
    'locale'         => '', // force gateway language  i.e. it_IT, es_ES, en_US ... (for express checkout only)
    'validate_ssl'   => true, // Validate SSL when creating api client.

    //Regular Paypal
    'client_id' => config('constants.paypal_client_id', ''),
    'secret' => config('constants.paypal_client_secret', ''),
    'settings' => array(
        'mode' => config('constants.paypal_environment', ''),
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path() . '/logs/paypal.log',
        'log.LogLevel' => 'ERROR'
    ),
];