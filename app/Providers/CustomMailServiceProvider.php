<?php

namespace App\Providers;

use Setting;
use Illuminate\Mail\MailServiceProvider;
use App\Customs\CustomTransportManager;


class CustomMailServiceProvider extends MailServiceProvider
{
    protected function registerSwiftTransport(){
        $this->app->singleton('swift.transport', function ($app) {
	        return new CustomTransportManager($app);
	    });
    }
}
