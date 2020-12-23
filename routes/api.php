<?php

use App\Http\Controllers\API\CameraController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\VehicleController;
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
    Route::group(['prefix' => 'devices','middleware' => 'auth:api'], function () {

        Route::get('/', [DeviceController::class, 'index']);
        Route::middleware('auth:api')->post('/', [DeviceController::class, 'create']);
        // Route::get('/{deviceId}/driveSummary', [DeviceController::class, 'driveSummery']);
        // Route::get('/{deviceId}/route', [DeviceController::class, 'getRoute']);
        // Route::get('/{deviceId}/cameras', [CameraController::class, 'getCameraByDeviceId']);
        // Route::post('/{deviceId}/switchon', [DeviceController::class, 'doWaitingQueue']);
    });

    // Vehicle APIs
    Route::group(['prefix' => 'vehicles','middleware' => 'auth:api'], function () {

        Route::get('/', [VehicleController::class, 'index']);
        Route::get('/{vehicle}', [VehicleController::class, 'show']);
        Route::put('/{vehicle}', [VehicleController::class, 'update']);
        Route::post('/', [VehicleController::class, 'create']);

//        Route::get('/{deviceId}/driveSummary', [VehicleController::class, 'driveSummery']);
//        Route::get('/{deviceId}/route', [VehicleController::class, 'getRoute']);
//        Route::get('/{deviceId}/cameras', [VehicleController::class, 'getCameraByDeviceId']);
//        Route::post('/{deviceId}/switchon', [VehicleController::class, 'doWaitingQueue']);
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
        Route::middleware('auth:api')->get('/{user}', [UserController::class, 'show']);
        Route::middleware('auth:api')->post('/', [UserController::class, 'store']);
        Route::middleware('auth:api')->put('/{user}', [UserController::class, 'update']);
        Route::middleware('auth:api')->delete('/{user}', [UserController::class, 'delete']);
    });
    // Role API
    Route::group(['prefix' => 'roles', 'middleware' => 'auth:api'], function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::get('/{role}', [RoleController::class, 'show']);
        Route::post('/', [RoleController::class, 'store']);
        Route::put('/{role}', [RoleController::class, 'update']);
        Route::delete('/{role}', [RoleController::class, 'delete']);
    });

    // Group API
    Route::group(['prefix' => 'groups', 'middleware' => 'auth:api'], function () {
        Route::get('/', [GroupController::class, 'index']);
        Route::get('/{group}', [GroupController::class, 'show']);
        Route::post('/', [GroupController::class, 'store']);
        Route::put('/{group}', [GroupController::class, 'update']);
        Route::delete('/{group}', [GroupController::class, 'destroy']);

        // add users to group
        Route::post('/{group}/users', [GroupController::class, 'addUsers']);

        // get users of group
        Route::get('/{group}/users', [GroupController::class, 'getUsers']);

        // get vehicles of group
        Route::get('/{group}/vehicles', [GroupController::class, 'getVehicles']);
    });

    // Customer API
    Route::group(['prefix' => 'customers', 'middleware' => 'auth:api'], function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::get('/{customer}', [CustomerController::class, 'show']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::put('/{customer}', [CustomerController::class, 'update']);
        Route::delete('/{customer}',[CustomerController::class, 'delete']);

        // get users of requested customer id
        Route::get('/{customer}/users', [CustomerController::class, 'indexUsers']);

        // get roles of requested customer id
        Route::get('/{customer}/roles', [CustomerController::class, 'indexRoles']);

        // get groups of requested customer id
        Route::get('/{customer}/groups', [CustomerController::class, 'indexGroups']);
    });

    // Driver APIs
    Route::group(['prefix' => 'drivers'], function () {
        Route::post('/', [DriverController::class, 'store']);
        Route::put('/{id}', [DriverController::class, 'update']);
        Route::delete('/{id}', [DriverController::class, 'destroy']);
        Route::get('/{id}', [DriverController::class, 'show']);
        Route::get('/', [DriverController::class, 'index']);
        Route::get('/{driverId}/history', [DriverController::class, 'getRegularDataByDriverId']);
    });
});
