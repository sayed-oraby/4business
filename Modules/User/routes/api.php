<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\ProfileController;

Route::middleware(['auth:sanctum'])->prefix('v1/profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');

    // Contact methods (WhatsApp & Calls)
    Route::get('/contact-methods', [ProfileController::class, 'getContactMethods'])->name('contact-methods.show');
    Route::put('/contact-methods', [ProfileController::class, 'updateContactMethods'])->name('contact-methods.update');

    // Notification settings
    Route::get('/notifications-settings', [ProfileController::class, 'getNotificationSettings'])->name('notifications.show');
    Route::put('/notifications-settings', [ProfileController::class, 'updateNotificationSettings'])->name('notifications.update');
});

