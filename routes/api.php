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

Route::group(['middleware' => ['api']], function () {
    Route::get('drink', 'API\DrinkController@index');
    Route::get('drink/{uuid}', 'API\DrinkController@show');
    Route::post('drink', 'API\DrinkController@store');
    Route::put('drink/{uuid}', 'API\DrinkController@update');
    Route::delete('drink/{uuid}', 'API\DrinkController@destroy');

    Route::get('usage', 'API\UsageController@index');
    Route::post('usage', 'API\UsageController@store');
});
