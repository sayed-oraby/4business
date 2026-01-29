<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\Dashboard\PageController;
use Modules\Page\Http\Controllers\Frontend\PageController as FrontendPageController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/
Route::middleware('web')
    ->name('frontend.page.')
    ->group(function () {
        Route::get('/about', [FrontendPageController::class, 'about'])->name('about');
        Route::get('/contact', [FrontendPageController::class, 'contact'])->name('contact');
        Route::post('/contact', [FrontendPageController::class, 'sendContact'])->name('send-contact');
        Route::get('/terms', [FrontendPageController::class, 'terms'])->name('terms');
        Route::get('/privacy', [FrontendPageController::class, 'privacy'])->name('privacy');
        Route::get('/page/{slug}', [FrontendPageController::class, 'show'])->name('show');
    });

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth:admin'])
    ->prefix('dashboard/pages')
    ->name('dashboard.pages.')
    ->group(function () {
        Route::get('/', [PageController::class, 'index'])
            ->middleware('permission:pages.view')
            ->name('index');

        Route::get('/data', [PageController::class, 'data'])
            ->middleware('permission:pages.view')
            ->name('data');

        Route::post('/', [PageController::class, 'store'])
            ->middleware('permission:pages.create')
            ->name('store');

        Route::delete('/bulk', [PageController::class, 'bulkDestroy'])
            ->middleware('permission:pages.delete')
            ->name('bulk-destroy');

        Route::put('/{page}', [PageController::class, 'update'])
            ->middleware('permission:pages.update')
            ->name('update');

        Route::delete('/{page}', [PageController::class, 'destroy'])
            ->middleware('permission:pages.delete')
            ->name('destroy');
    });
