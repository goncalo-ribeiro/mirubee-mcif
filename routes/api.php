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

Route::middleware('auth:api')->get('/products', 'ProductController@index');

Route::middleware('auth:api')->get('/sites', 'SiteController@index');
Route::middleware('auth:api')->post('/sites', 'SiteController@store');
Route::middleware('auth:api')->delete('/sites', 'SiteController@destroy');
Route::middleware('auth:api')->put('/sites', 'SiteController@update');

Route::middleware('auth:api')->post('/readings/threephase', 'ReadingThreePhaseController@store');

Route::middleware('auth:api')->get('/sites/{siteId}/readings/{start}/{end}', 'SiteController@getReadings');
Route::middleware('auth:api')->get('/sites/{siteId}/devices/', 'SiteController@getSiteDevices');
Route::middleware('auth:api')->get('/sites/{siteId}/devices/{deviceId}/readings/{start}/{end}', 'SiteController@getDeviceReadings');
Route::middleware('auth:api')->post('/sites/{siteId}/tariffs/', 'SiteController@setTariff');
Route::middleware('auth:api')->put('/sites/{siteId}/tariffs/', 'SiteController@updateTariff');
Route::middleware('auth:api')->delete('/sites/{siteId}/tariffs/', 'SiteController@deleteTariff');

Route::middleware('auth:api')->get('/alerts', 'AlertController@index');
Route::middleware('auth:api')->post('/alerts', 'AlertController@store');
Route::middleware('auth:api')->put('/alerts', 'AlertController@update');

// read notifications
Route::middleware('auth:api')->put('alerts/{alertId}/notifications', 'AlertController@readNotifications');
Route::middleware('auth:api')->delete('alerts/notifications/{notificationId}', 'AlertController@deleteNotification');
Route::middleware('auth:api')->delete('alerts/{alertId}/notifications', 'AlertController@deleteAlertNotifications');

Route::middleware('auth:api')->delete('/alerts/{alertId}/', 'AlertController@destroy');

Route::middleware('auth:api')->get('/devices', 'DeviceController@index');
Route::middleware('auth:api')->put('/devices/{deviceId}', 'DeviceController@update');

Route::middleware('auth:api')->get('users/email/{email}', 'UserControllerApi@userByEmail');

//Route::get('readings', 'ReadingThreePhaseController@index');

Route::middleware('auth:api')->get('reports', 'ReportController@getUserReports');