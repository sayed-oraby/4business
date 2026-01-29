<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\Api\BlogController;

Route::prefix('v1')
    ->name('api.blogs.')
    ->group(function () {
        Route::get('/blogs', [BlogController::class, 'index'])->name('index');
        Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('show');
    });
