<?php

use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\OperatorController;
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

        Route::get('/{deviceId}/driveSummary', [DeviceController::class, 'driveSummery']);
        Route::get('/{deviceId}/route', [DeviceController::class, 'getRoute']);
        Route::get('/{deviceId}/cameras', [CameraController::class, 'getCameraByDeviceId']);
        Route::post('/{deviceId}/switchon', [DeviceController::class, 'doWaitingQueue']);
    });

    // Event APIs
    Route::group(['prefix' => 'events'], function () {

        Route::get('/', [EventController::class, 'getEventsByDeviceId']);
        Route::get('/summary', [EventController::class, 'getEventSummary']);
    });

    // Operator APIs
    Route::group(['prefix' => 'operators'], function () {

        Route::get('/{operatorId}/driveSummary', [OperatorController::class, 'getDriveSummery']);
        Route::get('/{operatorId}/events', [OperatorController::class, 'getOperatorEvents']);
        Route::post('/{eventId}/videos', [VideoController::class, 'addEventVideos']);
    });

    // Event APIs
    Route::group(['prefix' => 'videos'], function () {

        Route::post('/', [VideoController::class, 'store']);
    });

    // Camera APIs
    Route::group(['prefix' => 'cameras'], function () {

        Route::post('/', [CameraController::class, 'store']);
        Route::put('/{camera}', [CameraController::class, 'update']);
        Route::delete('/{camera}', [CameraController::class, 'destroy']);
        Route::get('/{camera}', [CameraController::class, 'show']);
        Route::get('device/{device}', [CameraController::class, 'getCameraByDeviceId']);
    });
});
