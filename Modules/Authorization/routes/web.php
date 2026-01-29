<?php

use Illuminate\Support\Facades\Route;
use Modules\Authorization\Http\Controllers\Dashboard\RoleController;

Route::middleware(['web', 'auth:admin', 'permission:dashboard.access'])
    ->prefix('dashboard/authorization')
    ->name('dashboard.authorization.')
    ->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/data', [RoleController::class, 'data'])->name('roles.data');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::post('/roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions.sync');
        Route::get('/permissions/available', [RoleController::class, 'availablePermissions'])->name('permissions.available');
        Route::post('/permissions', [RoleController::class, 'createPermission'])->name('permissions.store');
    });
