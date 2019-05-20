<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// for plane 
Route::get('Plan', 'PlanController@index')->middleware('api_auth');
Route::post('Plan', 'PlanController@create')->middleware('api');
Route::delete('Plan/{id}', 'PlanController@destroy')->middleware('api');
Route::put('Plan/{id}', 'PlanController@update')->middleware('api');

//for service
Route::get('Service', 'ServiceController@index')->middleware('api');
Route::post('Service', 'ServiceController@create')->middleware('api');
Route::delete('Service/{id}', 'ServiceController@destroy')->middleware('api');
Route::put('Service/{id}', 'ServiceController@update')->middleware('api');
//for auth
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::group(['middleware' => 'auth:api'], function(){
Route::post('details', 'API\UserController@details');
Route::post('agent-login', 'API\AgentController@login');
Route::post('agent-register', 'API\AgentController@register');
});