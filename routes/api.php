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
use App\Http\Controllers\Api\GatewayController;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use App\Models\Alert;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// ─── IoT Gateway Simulation Routes (Public for simulation) ──────────────────
Route::post('/gateway/receive', [GatewayController::class, 'receive']);
Route::get('/gateway/status', [GatewayController::class, 'status']);

Route::get('/devices', function () {
    return DeviceResource::collection(Device::all());
});

Route::get('/devices/{device_id}', function ($device_id) {
    $device = Device::where('device_id', $device_id)->firstOrFail();
    return new DeviceResource($device);
});

Route::get('/alerts', function (Request $request) {
    $query = Alert::query();
    if ($request->status === 'open') {
        $query->whereNull('resolved_at');
    }
    return $query->latest()->get();
});

Route::patch('/alerts/{id}/resolve', function ($id) {
    $alert = Alert::findOrFail($id);
    $alert->update(['resolved_at' => now()]);
    return response()->json(['message' => 'Alert resolved']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Admin routes
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('device-types', DeviceTypeController::class);
    
    // Devices and Data (Simulation overrides these for now)
    // Route::apiResource('devices', DeviceController::class);
    Route::get('/devices/{device}/data', [DeviceDataController::class, 'index']);
    
    // Commands
    Route::get('/commands', [CommandController::class, 'index']);
    Route::post('/commands', [CommandController::class, 'store']);
    Route::get('/commands/{command}', [CommandController::class, 'show']);
    
    // IoT Callbacks (require token but no role check handled in controller)
    Route::patch('/commands/{command}/status', [CommandController::class, 'updateStatus']);
    Route::post('/devices/{device}/data', [DeviceDataController::class, 'store']);

    Route::post('/devices/{device}/data', [DeviceDataController::class, 'store']);
});
