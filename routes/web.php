<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\DeviceTypeController;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\AlertController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Global dashboard redirect
    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('operator.dashboard');
    })->name('dashboard');

    // Alerts Routes (Shared)
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::post('/alerts', [AlertController::class, 'store'])->name('alerts.store');
    Route::get('/alerts/{alert}', [AlertController::class, 'show'])->name('alerts.show');
    Route::patch('/alerts/{alert}/status', [AlertController::class, 'updateStatus'])->name('alerts.update-status');
    Route::post('/alerts/{alert}/respond', [AlertController::class, 'respond'])->name('alerts.respond');
    Route::post('/alerts/{alert}/messages', [AlertController::class, 'storeMessage'])->name('alerts.messages.store');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Analytics & Export Routes
        Route::get('/users-analytics', [UserController::class, 'analytics'])->name('users.analytics');
        Route::get('/users-export', [UserController::class, 'export'])->name('users.export');
        
        Route::get('/devices-analytics', [DeviceController::class, 'analytics'])->name('devices.analytics');
        Route::get('/devices-export', [DeviceController::class, 'export'])->name('devices.export');
        
        Route::get('/device-types-analytics', [DeviceTypeController::class, 'analytics'])->name('device-types.analytics');
        Route::get('/device-types-export', [DeviceTypeController::class, 'export'])->name('device-types.export');

        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::resource('users', UserController::class);
        
        Route::resource('roles', RoleController::class);
        Route::resource('device-types', DeviceTypeController::class);
        
        Route::patch('/devices/{device}/assign', [DeviceController::class, 'assignUser'])->name('devices.assign');
        Route::patch('/devices/{device}/toggle-status', [DeviceController::class, 'toggleStatus'])->name('devices.toggle-status');
        Route::resource('devices', DeviceController::class);
        
        Route::post('/devices/{device}/commands', [CommandController::class, 'store'])->name('commands.store');
        Route::get('/commands', [CommandController::class, 'history'])->name('commands.history');

        // Announcements
        Route::get('/announcements', [\App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements/send', [\App\Http\Controllers\Admin\AnnouncementController::class, 'send'])->name('announcements.send');
    });

    // Operator Routes
    Route::middleware(['role:operator'])->prefix('operator')->name('operator.')->group(function () {
        Route::get('/dashboard', [OperatorDashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
        Route::get('/devices/{device}', [DeviceController::class, 'show'])->name('devices.show');
        
        Route::post('/devices/{device}/commands', [CommandController::class, 'store'])->name('commands.store');
        
        Route::get('/commands', [CommandController::class, 'history'])->name('commands.history');
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notification Routes
    Route::get('/notifications/unread', function () {
        return response()->json([
            'count' => auth()->user()->unreadNotifications->count(),
            'notifications' => auth()->user()->unreadNotifications()->take(5)->get()
        ]);
    })->name('notifications.unread');

    Route::post('/notifications/{id}/mark-read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.mark-read');
});

require __DIR__.'/auth.php';
