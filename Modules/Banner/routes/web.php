<?php

use Illuminate\Support\Facades\Route;
use Modules\Banner\Http\Controllers\Dashboard\BannerController;

Route::middleware(['web', 'auth:admin'])
    ->prefix('dashboard/banners')
    ->name('dashboard.banners.')
    ->group(function () {
        Route::get('/', [BannerController::class, 'index'])
            ->middleware('permission:banners.view')
            ->name('index');

        Route::get('/data', [BannerController::class, 'data'])
            ->middleware('permission:banners.view')
            ->name('data');

        Route::post('/', [BannerController::class, 'store'])
            ->middleware('permission:banners.create')
            ->name('store');

        Route::delete('/bulk', [BannerController::class, 'bulkDestroy'])
            ->middleware('permission:banners.delete')
            ->name('bulk-destroy');

        Route::put('/{banner}', [BannerController::class, 'update'])
            ->middleware('permission:banners.update')
            ->name('update');

        Route::delete('/{banner}', [BannerController::class, 'destroy'])
            ->middleware('permission:banners.delete')
            ->name('destroy');
    });
