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

Route::post('login', 'LoginControllerApi@login');
Route::post('register', 'UserControllerApi@store');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:api')->get('/sites', 'SiteController@index');
Route::middleware('auth:api')->post('/sites', 'SiteController@store');
Route::middleware('auth:api')->delete('/sites', 'SiteController@destroy');
Route::middleware('auth:api')->put('/sites', 'SiteController@update');

Route::middleware('auth:api')->post('/readings/threephase', 'ReadingThreePhaseController@store');

Route::middleware('auth:api')->get('/sites/{siteId}/readings/{start}/{end}', 'SiteController@getReadings');
Route::middleware('auth:api')->get('/sites/{siteId}/devices/', 'SiteController@getSiteDevices');
Route::middleware('auth:api')->get('/sites/{siteId}/devices/{deviceId}/readings/{start}/{end}', 'SiteController@getDeviceReadings');

Route::middleware('auth:api')->get('/devices', 'DeviceController@index');
Route::middleware('auth:api')->put('/devices/{deviceId}', 'DeviceController@update');

Route::middleware('auth:api')->get('users/email/{email}', 'UserControllerApi@userByEmail');

Route::get('readings', 'ReadingThreePhaseController@index');