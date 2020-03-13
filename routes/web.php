<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');*/
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
//Route::get('/payment/{paymentId}/{planename?}/{fee?}/{firstName?}/{lastName?}/{countPerson?}/
//{burialFee?}/{seminarFee?}', 'paymentcontroller@index')->name('payment');
//Route::get('/payment/{paymentId}/{planename?}/{fee?}/{firstName?}/{lastName?}/{countPerson?}/
//{burialFee?}/{seminarFee?}', 'paymentcontroller@index')->name('payment');
Route::get('/payment/{paymentId}/{planename?}/{fee?}/{firstName?}/{lastName?}/{countPerson?}/{burialFee?}/{seminarFee?}/{initianFee?}/{burialCity?}/{burialState?}', 'paymentcontroller@index')->name('payment');
Route::get('send_test_email', function(){
    //$mail = mail("satyam.bargad@gmail.com","My subject",'hi');
   Mail::raw('Sending emails with Mailgun and Laravel is easy!', function($message)
   {
       $message->subject('Mailgun and Laravel are awesome!');
       $message->from('admin@gmnotices.com', 'Website Name');
       $message->to('satyam.bargad@gmail.com');
   });


    echo "test";exit;
});
Route::post('/store', 'paymentcontroller@store');
Route::post('/paymentSucess', 'paymentcontroller@paymentSucess')->name('paymentSucess');
//Route::get('/payment/{mem_data}', 'paymentcontroller@index')->name('payment');
