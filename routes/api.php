<?php

use Illuminate\Http\Request;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AppointmentController;

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

 
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


Route::post('login', 'API\AuthController@login')->withoutMiddleware('throttle:api');

// List of nature of Enquiry
Route::get('/natureofenquiry', 'API\AppointmentController@natureofenquiry')->withoutMiddleware('throttle:api');

// List of Service Type
Route::get('/servicetype', 'API\AppointmentController@servicetype')->withoutMiddleware('throttle:api');

Route::prefix('appointments')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', 'API\AppointmentController@index')->withoutMiddleware('throttle:api'); // List
        Route::post('/', 'API\AppointmentController@store')->withoutMiddleware('throttle:api'); // Create
        Route::get('/{id}', 'API\AppointmentController@show')->withoutMiddleware('throttle:api'); // Show
        Route::put('/{id}', 'API\AppointmentController@update')->withoutMiddleware('throttle:api'); // Update
        Route::delete('/{id}', 'API\AppointmentController@destroy')->withoutMiddleware('throttle:api'); // Delete
        Route::post('logout', 'API\AuthController@logout')->withoutMiddleware('throttle:api');
    });
});
