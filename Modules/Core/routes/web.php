<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Frontend\HomeController;
use Modules\Core\Http\Controllers\LocaleController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/
Route::middleware('web')
    ->name('frontend.')
    ->group(function () {
        // Home page
        // Route::get('/', [HomeController::class, 'index'])->name('home');

        Route::get('/', function () {
            return 'welcome';
        })->name('home');

        // AJAX load more posts
        Route::get('/load-more-posts', [HomeController::class, 'loadMore'])->name('home.loadMore');
    });

// Locale switching (without frontend. prefix)
Route::middleware('web')->group(function () {
    Route::get('/lang/{locale?}', LocaleController::class)->name('core.locale.switch');
    Route::get('/language/{locale?}', LocaleController::class)->name('language.switch');
    Route::get('/{locale}', LocaleController::class)
        ->whereIn('locale', array_keys(config('app.available_locales', ['en' => []])))
        ->name('core.locale.shortcut');
});

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth:admin', 'permission:dashboard.access'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        Route::get('/', [\Modules\Core\Http\Controllers\Dashboard\DashboardController::class, 'index'])->name('home');
    });
