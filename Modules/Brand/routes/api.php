<?php

use Illuminate\Support\Facades\Route;
use Modules\Brand\Http\Controllers\Api\BrandController;

Route::prefix('v1')
    ->name('api.brands.')
    ->group(function () {
        Route::get('/brands', [BrandController::class, 'index'])->name('index');
    });
