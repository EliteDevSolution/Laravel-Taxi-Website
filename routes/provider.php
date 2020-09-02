<?php

/*
|--------------------------------------------------------------------------
| Provider Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'ProviderController@index')->name('index');
Route::get('/trips', 'ProviderResources\TripController@history')->name('trips');

Route::get('/incoming', 'ProviderController@incoming')->name('incoming');
Route::post('/request/{id}', 'ProviderController@accept')->name('accept');
Route::patch('/request/{id}', 'ProviderController@update')->name('update');
Route::post('/request/{id}/rate', 'ProviderController@rating')->name('rating');
Route::delete('/request/{id}', 'ProviderController@reject')->name('reject');

Route::get('/earnings', 'ProviderController@earnings')->name('earnings');
Route::get('/upcoming', 'ProviderController@upcoming_trips')->name('upcoming');
Route::post('/cancel', 'ProviderController@cancel')->name('cancel');

Route::resource('documents', 'ProviderResources\DocumentController');

Route::get('/profile', 'ProviderResources\ProfileController@show')->name('profile.index');
Route::post('/profile', 'ProviderResources\ProfileController@store')->name('profile.update');

Route::get('/location', 'ProviderController@location_edit')->name('location.index');
Route::post('/location', 'ProviderController@location_update')->name('location.update');

Route::get('/profile/password', 'ProviderController@change_password')->name('change.password');
Route::post('/change/password', 'ProviderController@update_password')->name('password.update');

Route::post('/profile/available', 'ProviderController@available')->name('available');
Route::get('/wallet_transation', 'ProviderController@wallet_transation')->name('wallet.transation');
Route::post('/wallet_transation/details', 'ProviderController@wallet_details')->name('wallet.details');
Route::get('/transfer', 'ProviderController@transfer')->name('transfer');
Route::post('/transfer/send', 'ProviderController@requestamount')->name('requestamount');
Route::get('/transfer/cancel', 'ProviderController@requestcancel')->name('requestcancel');

Route::get('/stripe/account', 'ProviderController@stripe');

Route::get('cards', 'ProviderController@cards')->name('cards');
Route::post('card/store', 'Resource\ProviderCardResource@store');
Route::post('card/set', 'Resource\ProviderCardResource@set_default');
Route::delete('card/destroy', 'Resource\ProviderCardResource@destroy');
Route::get('referral', 'ProviderController@referral')->name('referral');
//notifications
Route::get('notifications', 'ProviderController@notifications');
//Dispute
Route::get('/dispute/{id}', 'ProviderController@dispute');
Route::post('/dispute', 'ProviderController@dispute_store');
//waiting time
Route::post('/waiting', 'ProviderResources\TripController@waiting');

// wallet
Route::post('/add/money', 'PaymentController@add_money');
