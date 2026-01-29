<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\Api\PageController;

Route::prefix('v1')
    ->name('api.pages.')
    ->group(function () {
        Route::get('/pages', [PageController::class, 'index'])->name('index');
    });
