<?php

use Illuminate\Support\Facades\Route;
use Modules\Activity\Http\Controllers\Api\NotificationDeviceController;

// Optional auth - authenticates if token present, allows guests otherwise
Route::prefix('v1/notifications')->middleware('optional.auth')->group(function () {
    Route::post('/devices', [NotificationDeviceController::class, 'store'])->name('notifications.devices.store');
    Route::put('/devices/{uuid}', [NotificationDeviceController::class, 'update'])->name('notifications.devices.update');
    Route::delete('/devices/{uuid}', [NotificationDeviceController::class, 'destroy'])->name('notifications.devices.destroy');
});
