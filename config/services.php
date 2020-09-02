<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    
    'facebook' => [
        'client_id' => config('constants.facebook_client_id'),
        'client_secret' => config('constants.facebook_client_secret'),
        'redirect' => config('constants.facebook_redirect'),
    ],

    'google' => [
        'client_id' => config('constants.google_client_id'),
        'client_secret' => config('constants.google_client_secret'),
        'redirect' => config('constants.google_redirect'),
    ],

    'paytm-wallet' => [

        'env' => config('constants.paytm_environment'), // values : (local | production)
        'merchant_id' => config('constants.paytm_merchant_id'),
        'merchant_key' => config('constants.paytm_merchant_key'),
        'merchant_website' => config('constants.paytm_website'),
        'channel' => config('constants.paytm_channel'),
        'industry_type' => config('constants.paytm_industry_type'),

    ],

];
