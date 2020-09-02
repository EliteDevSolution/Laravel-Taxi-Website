<?php 

namespace App\Customs;

use Illuminate\Mail\TransportManager;
use Setting;

class CustomTransportManager extends TransportManager {

    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;

        if( config('constants.send_email')==1 ){
            
            $this->app['config']['mail'] = [
                'driver'        => config('constants.mail_driver'),
                'host'          => config('constants.mail_host'),
                'port'          => config('constants.mail_port'),
                'from'          => [
                'address'   => config('constants.mail_from_address'),
                'name'      => config('constants.mail_from_name')
                ],
                'encryption'    => config('constants.mail_encryption'),
                'username'      => config('constants.mail_username'),
                'password'      => config('constants.mail_password'),
                'sendmail'      => '/usr/sbin/sendmail -bs',
                'pretend'       => false,
            ];

            if(config('constants.mail_driver')=='mailgun'){
               $this->app['config']['services'] = [
                    'mailgun' => [
                        'domain' => config('constants.mail_domain'),
                        'secret' => config('constants.mail_secret'),
                    ]
                ];
            }     
       }

    }
}