<?php

use App\Http\Controllers\API\CameraController;
use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\DriverController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\OperatorController;
use App\Http\Controllers\API\RfidController;
use App\Http\Controllers\API\RfidHistoryController;
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

        Route::get('/', [EventController::class, 'getEvents']);
        Route::get('/summary', [EventController::class, 'getEventSummary']);
        Route::get('/{eventId}', [EventController::class, 'getEventById']);
        Route::get('/video/{eventId}', [EventController::class, 'getEventVideoById']);
        Route::post('/{eventId}/videos', [VideoController::class, 'addEventVideos']);
    });

    // Operator APIs
    Route::group(['prefix' => 'operators'], function () {

        Route::get('/{operatorId}/driveSummary', [OperatorController::class, 'getDriveSummery']);
        Route::get('/{operatorId}/events', [OperatorController::class, 'getOperatorEvents']);
    });

    // Video APIs
    Route::group(['prefix' => 'videos'], function () {

        Route::post('/', [VideoController::class, 'store']);
        Route::post('/{eventId}/videos', [VideoController::class, 'addEventVideos']);
    });

    // Camera APIs
    Route::group(['prefix' => 'cameras'], function () {

        Route::post('/', [CameraController::class, 'store']);
        Route::put('/{camera}', [CameraController::class, 'update']);
        Route::delete('/{camera}', [CameraController::class, 'destroy']);
        Route::get('/{camera}', [CameraController::class, 'show']);
    });

    // Driver APIs
    Route::group(['prefix' => 'drivers'], function () {

        Route::post('/', [DriverController::class, 'store']);
        Route::put('/{driver}', [DriverController::class, 'update']);
        Route::delete('/{driver}', [DriverController::class, 'destroy']);
        Route::get('/{driver}', [DriverController::class, 'show']);
        Route::get('/', [DriverController::class, 'index']);
    });

    // RFID APIs
    Route::group(['prefix' => 'rfid'], function () {

        Route::post('/', [RfidController::class, 'store']);
        Route::put('/{id}', [RfidController::class, 'update']);
        Route::delete('/{id}', [RfidController::class, 'destroy']);
        Route::get('/{id}', [RfidController::class, 'show']);
        Route::get('/', [RfidController::class, 'index']);
    });

    // RFID History APIs
    Route::group(['prefix' => 'rfid'], function () {
        Route::post('/history', [RfidHistoryController::class, 'store']);
    });
});
