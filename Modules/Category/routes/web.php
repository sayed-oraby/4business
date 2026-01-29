<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\Dashboard\CategoryController;

Route::middleware(['web', 'auth:admin'])
    ->prefix('dashboard/categories')
    ->name('dashboard.categories.')
    ->group(function () {
        Route::get('/', [CategoryController::class, 'index'])
            ->middleware('permission:categories.view')
            ->name('index');

        Route::get('/data', [CategoryController::class, 'data'])
            ->middleware('permission:categories.view')
            ->name('data');

        Route::get('/parents', [CategoryController::class, 'parents'])
            ->middleware('permission:categories.view')
            ->name('parents');

        Route::post('/', [CategoryController::class, 'store'])
            ->middleware('permission:categories.create')
            ->name('store');

        Route::put('/{category}', [CategoryController::class, 'update'])
            ->middleware('permission:categories.update')
            ->name('update');

        Route::delete('/{category}', [CategoryController::class, 'destroy'])
            ->middleware('permission:categories.delete')
            ->name('destroy');

        Route::delete('/bulk', [CategoryController::class, 'bulkDestroy'])
            ->middleware('permission:categories.delete')
            ->name('bulk-destroy');
    });
