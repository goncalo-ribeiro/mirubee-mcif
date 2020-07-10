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

Route::group(['namespace'=>'\DestruidorPT\LaravelSQRLAuth\App\Http\Controllers'], function() {
    Route::post('sqrl', 'SQRL\SQRLControllerAPI@sqrl');                 # Route of API SQRL
    Route::get('/sqrl', 'SQRL\SQRLControllerAPI@checkIfisReady');       # Route to check if the nonce is verified
});

Route::post('login', 'LoginControllerApi@login');
Route::post('register', 'UserControllerApi@store');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('multi_factor_authentication:api')->get('/mfa/test', function (Request $request) {
    return response()->json(['message' => 'mfa OK'], 200);;
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

// mfa
Route::middleware('auth:api')->post('/mfa/setup/activationEmail', 'MfaMethodController@sendActivationEmail');
Route::middleware('auth:api')->post('/mfa/setup/activateEmail', 'MfaMethodController@activateEmail');
Route::middleware('auth:api')->post('/mfa/setup/email', 'MfaMethodController@enableEmail');
Route::middleware('auth:api')->delete('/mfa/setup/email', 'MfaMethodController@disableEmail');

Route::middleware('auth:api')->post('/mfa/auth/email', 'MfaMethodController@authenticateThroughEmail');
Route::middleware('auth:api')->post('/mfa/auth/email/code', 'MfaMethodController@sendAuthenticationEmail');

Route::middleware('auth:api')->post('/mfa/setup/activateGoogle', 'MfaMethodController@activateGoogle');
Route::middleware('auth:api')->post('/mfa/setup/google', 'MfaMethodController@enableGoogle');
Route::middleware('auth:api')->delete('/mfa/setup/google', 'MfaMethodController@disableGoogle');
Route::middleware('auth:api')->post('/mfa/auth/google', 'MfaMethodController@authenticateThroughGoogle');
Route::middleware('auth:api')->delete('/mfa/setup/u2f', 'MfaMethodController@disableU2F');

Route::middleware('auth:api')->get('/mfa/setup/sqrlNonce', 'MfaMethodController@getSqrlNonce');
Route::middleware('auth:api')->post('/mfa/sqrl', 'MfaMethodController@loginSqrl');
Route::middleware('auth:api')->delete('/mfa/setup/sqrl', 'MfaMethodController@disableSQRL');

//u2f
Route::middleware('auth:api')->get('/u2f/createArgs', 'U2FController@getCreateArgs');
Route::middleware('auth:api')->post('/u2f/processCreate', 'U2FController@processCreate');
Route::middleware('auth:api')->get('/u2f/getArgs', 'U2FController@getGetArgs');
Route::middleware('auth:api')->post('/u2f/processGet', 'MfaMethodController@disableSQRL');

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