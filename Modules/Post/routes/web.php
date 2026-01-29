<?php

use Illuminate\Support\Facades\Route;
use Modules\Post\Http\Controllers\Dashboard\PackageController;
use Modules\Post\Http\Controllers\Dashboard\PostController;
use Modules\Post\Http\Controllers\Dashboard\PostTypeController;
use Modules\Post\Http\Controllers\Frontend\PostController as FrontendPostController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

Route::middleware('web')
    ->name('frontend.')
    ->group(function () {
        Route::get('/posts', [FrontendPostController::class, 'index'])->name('posts.index');
        Route::get('/posts/create', [FrontendPostController::class, 'create'])->name('posts.create')->middleware('auth:admin');
        Route::post('/posts', [FrontendPostController::class, 'store'])->name('posts.store')->middleware('auth:admin');
        Route::get('/posts/{slug}', [FrontendPostController::class, 'show'])->name('posts.show');

        // Payment callback route (public - accessed by MyFatoorah gateway)
        Route::any('/posts/{uuid}/payment/callback', [FrontendPostController::class, 'paymentCallback'])
            ->name('posts.payment.callback');

        // User post management routes
        Route::middleware('auth:admin')->group(function () {

            Route::get('/posts/{uuid}/edit', [FrontendPostController::class, 'edit'])->name('posts.edit');
            Route::put('/posts/{uuid}', [FrontendPostController::class, 'update'])->name('posts.update');
            Route::delete('/posts/{uuid}', [FrontendPostController::class, 'destroy'])->name('posts.destroy');

            Route::delete('posts/{post}/attachments/{attachment}', [FrontendPostController::class, 'deleteAttachment'])->name('posts.attachments.destroy');
            Route::patch('/posts/{uuid}/stop', [FrontendPostController::class, 'stop'])->name('posts.stop');
            Route::patch('/posts/{uuid}/resume', [FrontendPostController::class, 'resume'])->name('posts.resume');

            // Payment retry route
            Route::post('/posts/{uuid}/payment/retry', [FrontendPostController::class, 'retryPayment'])
                ->name('posts.payment.retry');

        });
    });

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::resource('posts', PostController::class);
    Route::patch('posts/{post}/status', [PostController::class, 'updateStatus'])->name('posts.status');
    Route::delete('posts/{post}/attachments/{attachment}', [PostController::class, 'deleteAttachment'])->name('posts.attachments.destroy');

    Route::resource('post-types', PostTypeController::class);
    Route::resource('packages', PackageController::class);
});
