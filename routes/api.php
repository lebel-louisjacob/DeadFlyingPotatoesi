<?php

use App\Providers\AuthServiceProvider;
use App\Station;
use App\Users\Admin;
use App\Users\StationOwner;
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

Route::post('mail/subscribe', 'EmailController@subscribe');

Route::post('forgotPassword', 'EmailController@forgotPassword');
Route::post('validateResetToken', 'UserController@validateResetToken');
Route::post('resetPassword', 'UserController@resetPassword');

/* All the routes associated to login */
Route::post('login', 'LoginController@login');
Route::post('logout', 'LoginController@logout')->middleware('auth:api');

/* All the routes associated to register */
Route::post('register', 'RegisterController@register');

/* All the routes associates to station (Private) */
Route::group(['middleware' => ['auth:api', 'scope:'.Admin::SCOPE]], function() {
    Route::post('stations', 'StationController@store');
    Route::put('stations/{stations}', 'StationController@update');
    Route::delete('stations/{stations}', 'StationController@destroy');
    Route::post('stations/{stations}/sensors/{sensors}/readings', 'SensorReadingController@store');
});

/* All route associated to Comment */
Route::get('stations/{stations}/comments', 'CommentController@showAllStationsComments');
Route::middleware('auth:api')->post('stations/{stations}/comments', 'CommentController@store');


Route::get('stations', 'StationController@showAllPublicStations');
Route::get('stations/{stations}', 'StationController@showPublicStation');
Route::get('stations/{stations}/sensors', 'SensorController@showAllPublicSensors');
Route::get('stations/{stations}/sensors/latest-aqi', 'StationController@showLatestAqi');
Route::get('stations/{stations}/sensors/{sensors}', 'SensorController@showPublicSensor');
Route::get('stations/{stations}/sensors/{sensors}/readings', 'SensorReadingController@showAllPublicSensorReadings');
Route::get('stations/{stations}/sensors/{sensors}/readings/latest-values', 'SensorController@showLatestValues');
Route::get('stations/{stations}/sensors/{sensors}/{type}/readings', 'SensorReadingController@showPublicReadingsByType');
Route::get('stations/{stations}/sensors/{sensors}/{type}/history', 'HistoryController@showHistory');
