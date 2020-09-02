<?php

return array(

    'IOSUser'     => array(
        'environment' => config('constants.environment'),
        'certificate' => storage_path().'/app/public/apns/user.pem',
        'passPhrase'  => config('constants.ios_push_password'),
        'service'     => 'apns'
    ),
    'IOSProvider' => array(
        'environment' => config('constants.environment'),
        'certificate' => storage_path().'/app/public/apns/provider.pem',
        'passPhrase'  => config('constants.ios_push_password'),
        'service'     => 'apns'
    ),
    'Android' => array(
        'environment' => config('constants.environment'),
        'apiKey'      => config('constants.android_push_key'),
        'service'     => 'gcm'
    )

);