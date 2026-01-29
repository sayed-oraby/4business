<?php

use Illuminate\Support\Facades\Route;
use Modules\Banner\Http\Controllers\Api\BannerController;

Route::prefix('v1')
    ->name('api.banners.')
    ->group(function () {
        Route::get('/banners', [BannerController::class, 'index'])->name('index');
    });
