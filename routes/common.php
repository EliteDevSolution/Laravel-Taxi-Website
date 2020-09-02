<?php


Route::post('/save-subscription/{id}/{guard}', 'HomeController@save_subscription')->name('save_subscription');


Route::get('/provider/payment/response', 'PaymentController@response')->name('payment.success');

Route::get('/provider/payment/failure', 'PaymentController@failure')->name('payment.failure');

Route::post('/provider/payment/response', 'PaymentController@paytm_response');

Route::post('/provider/payment/failure', 'PaymentController@payu_error');


//paytm
Route::post('/provider/paytm/response', 'PaymentController@paytm_response');