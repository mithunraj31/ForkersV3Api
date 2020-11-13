<?php

use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\CameraController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// API version 1 group
Route::group(['prefix' => 'v1'], function () {
    // Device APIs
    Route::group(['prefix' => 'devices'], function () {

        Route::get('/', [DeviceController::class, 'index']);
    });

    // Event APIs
    Route::group(['prefix' => 'events'], function () {

        Route::get('/summary', [EventController::class, 'getEventSummary']);
    });

    // Video APIs
    Route::group(['prefix' => 'videos'], function () {

        Route::post('/', [VideoController::class, 'store']);
        Route::get('/', [VideoController::class, 'index']);
        Route::put('/{video}', [VideoController::class, 'update']);
        Route::delete('/{video}', [VideoController::class, 'destroy']);
    });
});
