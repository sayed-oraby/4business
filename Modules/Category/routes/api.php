<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\Api\CategoryController;

Route::prefix('v1')
    ->name('api.categories.')
    ->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('index');
    });
