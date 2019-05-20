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
Route::get('Plan', 'PlanController@index');
Route::post('Plan', 'PlanController@create');
Route::delete('Plan/{id}', 'PlanController@destroy');
Route::put('Plan/{id}', 'PlanController@update');

//for service
Route::get('Service', 'ServiceController@index');
Route::post('Service', 'ServiceController@create');
Route::delete('Service/{id}', 'ServiceController@destroy');
Route::put('Service/{id}', 'ServiceController@update');
//for auth
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::post('details', 'API\UserController@details');
Route::post('agent-login', 'API\AgentController@login');
Route::post('agent-register', 'API\AgentController@register');
Route::post('Customber', 'API\CustomberController@register');
