<?php

use App\Http\Controllers\API\CameraController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\DriverController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\GroupController;
use App\Http\Controllers\API\OperatorController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\UserController;
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

    // User API
    Route::group(['prefix' => 'users'], function () {
        Route::middleware('auth:api')->get('/', [UserController::class, 'index']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{user}', [UserController::class, 'update']);
    });
    // Role API
    Route::group(['prefix' => 'roles', 'middleware' => 'auth:api'], function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::get('/{role}', [RoleController::class, 'show']);
        Route::post('/', [RoleController::class, 'store']);
        Route::put('/{role}', [RoleController::class, 'update']);
    });

    // Group API
    Route::group(['prefix' => 'groups', 'middleware' => 'auth:api'], function () {
        Route::get('/', [GroupController::class, 'index']);
        Route::get('/{group}', [GroupController::class, 'show']);
        Route::post('/', [GroupController::class, 'store']);
        Route::put('/{group}', [GroupController::class, 'update']);
    });

    // Customer API
    Route::group(['prefix' => 'customers', 'middleware' => 'auth:api'], function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::get('/{customer}', [CustomerController::class, 'show']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::put('/{customer}', [CustomerController::class, 'update']);
        Route::delete('/{customer}', [CustomerController::class, 'delete']);
    });

    // Driver APIs
    Route::group(['prefix' => 'operators'], function () {
        Route::post('/', [DriverController::class, 'store']);
        Route::put('/{id}', [DriverController::class, 'update']);
        Route::delete('/{id}', [DriverController::class, 'destroy']);
        Route::get('/{id}', [DriverController::class, 'show']);
        Route::get('/', [DriverController::class, 'index']);
    });
});
