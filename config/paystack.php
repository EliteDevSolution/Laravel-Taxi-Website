<?php 

return [

    /**
     * Public Key From Paystack Dashboard
     *
     */
    'publicKey' => config('constants.paystack_public_key', ''),

    /**
     * Secret Key From Paystack Dashboard
     *
     */
    'secretKey' => config('constants.paystack_secret_key', ''),

    /**
     * Paystack Payment URL
     *
     */
    'paymentUrl' => 'https://api.paystack.co',

    /**
     * Optional email address of the merchant
     *
     */
    'merchantEmail' => 'payment.test@preiac.com',

];