<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\OperatorController;
use App\Models\Event;
use Illuminate\Foundation\Console\EventCacheCommand;
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

        Route::get('/driveSummary', [DeviceController::class, 'driveSummery']);
    });

    // Event APIs
    Route::group(['prefix' => 'events'], function () {

        Route::get('/', [EventController::class, 'getEventsByDeviceId']);
        Route::get('/summary', [EventController::class, 'getEventSummary']);
    });

    // Operator APIs
    Route::group(['prefix' => 'operators'], function () {

        Route::get('/{operatorId}/driveSummary', [OperatorController::class, 'getDriveSummery']);
    });
});
