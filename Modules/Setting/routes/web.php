<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\Dashboard\ContactMessageController;
use Modules\Setting\Http\Controllers\SettingController;

Route::middleware(['web', 'auth:admin', 'permission:dashboard.access'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Contact Messages - رسائل اتصل بنا
        Route::prefix('contact-messages')->name('contact-messages.')->group(function () {
            Route::get('/', [ContactMessageController::class, 'index'])->name('index');
            Route::get('/data', [ContactMessageController::class, 'data'])->name('data');
            Route::get('/{contactMessage}', [ContactMessageController::class, 'show'])->name('show');
            Route::put('/{contactMessage}/status', [ContactMessageController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [ContactMessageController::class, 'bulkDestroy'])->name('bulk-delete');
        });
    });
