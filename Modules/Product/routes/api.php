<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Api\ProductController;
use Modules\Product\Http\Controllers\Api\ProductTagController;

Route::prefix('v1')
    ->name('api.products.')
    ->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('index');
        Route::get('/products/{id}', [ProductController::class, 'show'])->name('show');
    });

Route::prefix('v1')
    ->name('api.tags.')
    ->group(function () {
        Route::get('/tags', [ProductTagController::class, 'index'])->name('index');
        Route::get('/tags/{id}', [ProductTagController::class, 'show'])->name('show');
    });
