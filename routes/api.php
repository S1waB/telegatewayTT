<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\DeviceTypeController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\CommandController;
use App\Http\Controllers\Api\DeviceDataController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Admin routes
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('device-types', DeviceTypeController::class);
    
    // Devices and Data
    Route::apiResource('devices', DeviceController::class);
    Route::get('/devices/{device}/data', [DeviceDataController::class, 'index']);
    
    // Commands
    Route::get('/commands', [CommandController::class, 'index']);
    Route::post('/commands', [CommandController::class, 'store']);
    Route::get('/commands/{command}', [CommandController::class, 'show']);
    
    // IoT Callbacks (require token but no role check handled in controller)
    Route::patch('/commands/{command}/status', [CommandController::class, 'updateStatus']);
    Route::post('/devices/{device}/data', [DeviceDataController::class, 'store']);
});
