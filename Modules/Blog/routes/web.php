<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\Dashboard\BlogController;
use Modules\Blog\Http\Controllers\Dashboard\BlogGalleryController;
use Modules\Blog\Http\Controllers\Dashboard\BlogTagController;

Route::middleware(['web', 'auth:admin'])
    ->prefix('dashboard/blogs')
    ->name('dashboard.blogs.')
    ->group(function () {
        Route::get('/', [BlogController::class, 'index'])
            ->middleware('permission:blogs.view')
            ->name('index');

        Route::get('/data', [BlogController::class, 'data'])
            ->middleware('permission:blogs.view')
            ->name('data');

        Route::post('/', [BlogController::class, 'store'])
            ->middleware('permission:blogs.create')
            ->name('store');

        Route::put('/{blog}', [BlogController::class, 'update'])
            ->middleware('permission:blogs.update')
            ->name('update');

        Route::delete('/{blog}', [BlogController::class, 'destroy'])
            ->middleware('permission:blogs.delete')
            ->name('destroy');

        Route::delete('/bulk', [BlogController::class, 'bulkDestroy'])
            ->middleware('permission:blogs.delete')
            ->name('bulk-destroy');

        Route::get('/{blog}/gallery', [BlogController::class, 'gallery'])
            ->middleware('permission:blogs.view')
            ->name('gallery');

        Route::post('/gallery/upload', [BlogGalleryController::class, 'upload'])
            ->middleware('permission:blogs.create|blogs.update')
            ->name('gallery.upload');

        Route::delete('/gallery/{gallery}', [BlogGalleryController::class, 'destroy'])
            ->middleware('permission:blogs.create|blogs.update')
            ->name('gallery.destroy');

        Route::get('/tags', [BlogTagController::class, 'index'])
            ->middleware('permission:blogs.view')
            ->name('tags.index');

        Route::post('/tags', [BlogTagController::class, 'store'])
            ->middleware('permission:blogs.create')
            ->name('tags.store');
    });
