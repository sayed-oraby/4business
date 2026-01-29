<?php

use Illuminate\Support\Facades\Route;
use Modules\Brand\Http\Controllers\Dashboard\BrandController;

Route::middleware(['web', 'auth:admin'])
    ->prefix('dashboard/brands')
    ->name('dashboard.brands.')
    ->group(function () {
        Route::get('/', [BrandController::class, 'index'])
            ->middleware('permission:brands.view')
            ->name('index');

        Route::get('/data', [BrandController::class, 'data'])
            ->middleware('permission:brands.view')
            ->name('data');

        Route::post('/', [BrandController::class, 'store'])
            ->middleware('permission:brands.create')
            ->name('store');

        Route::put('/{brand}', [BrandController::class, 'update'])
            ->middleware('permission:brands.update')
            ->name('update');

        Route::delete('/{brand}', [BrandController::class, 'destroy'])
            ->middleware('permission:brands.delete')
            ->name('destroy');

        Route::delete('/bulk', [BrandController::class, 'bulkDestroy'])
            ->middleware('permission:brands.delete')
            ->name('bulk-destroy');
    });
