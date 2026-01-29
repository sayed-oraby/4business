<?php

use Illuminate\Support\Facades\Route;
use Modules\Activity\Http\Controllers\Dashboard\AuditLogController;
use Modules\Activity\Http\Controllers\Dashboard\NotificationController;

Route::middleware(['web', 'auth:admin', 'permission:dashboard.access'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/data', [AuditLogController::class, 'data'])->name('audit-logs.data');
        Route::get('/audit-logs/users', [AuditLogController::class, 'users'])->name('audit-logs.users');
        Route::get('/notifications/feed', [NotificationController::class, 'feed'])->name('notifications.feed');
        Route::post('/notifications/mark-all', [NotificationController::class, 'markAll'])->name('notifications.mark-all');
        Route::get('/notifications/important', [NotificationController::class, 'importantIndex'])->name('notifications.important.index');
        Route::get('/notifications/important/data', [NotificationController::class, 'importantData'])->name('notifications.important.data');
    });
