<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Dashboard\ProductController;

Route::middleware(['web', 'auth:admin'])
    ->prefix('dashboard/products')
    ->name('dashboard.products.')
    ->group(function () {
        Route::get('/', [ProductController::class, 'index'])
            ->middleware('permission:products.view')
            ->name('index');

        Route::get('/data', [ProductController::class, 'data'])
            ->middleware('permission:products.view')
            ->name('data');

        Route::get('/create', [ProductController::class, 'create'])
            ->middleware('permission:products.create')
            ->name('create');

        Route::get('/{product}/edit', [ProductController::class, 'edit'])
            ->middleware('permission:products.update')
            ->name('edit');

        Route::post('/', [ProductController::class, 'store'])
            ->middleware('permission:products.create')
            ->name('store');

        Route::put('/{product}', [ProductController::class, 'update'])
            ->middleware('permission:products.update')
            ->name('update');

        Route::delete('/{product}', [ProductController::class, 'destroy'])
            ->middleware('permission:products.delete')
            ->name('destroy');

        Route::delete('/bulk', [ProductController::class, 'bulkDestroy'])
            ->middleware('permission:products.delete')
            ->name('bulk-destroy');

        Route::post('/gallery', [ProductController::class, 'galleryUpload'])
            ->middleware('permission:products.create|products.update')
            ->name('gallery.upload');

        Route::get('/{product}/gallery', [ProductController::class, 'gallery'])
            ->middleware('permission:products.view')
            ->name('gallery.index');

        Route::delete('/gallery/{gallery}', [ProductController::class, 'galleryDestroy'])
            ->middleware('permission:products.create|products.update')
            ->name('gallery.destroy');

        Route::get('/tags/options', [ProductController::class, 'tagsIndex'])
            ->middleware('permission:products.view')
            ->name('tags.index');

        Route::post('/tags', [ProductController::class, 'tagsStore'])
            ->middleware('permission:products.create|products.update')
            ->name('tags.store');

        Route::get('/categories', [ProductController::class, 'categories'])
            ->middleware('permission:products.view')
            ->name('categories');

        Route::get('/brands', [ProductController::class, 'brands'])
            ->middleware('permission:products.view')
            ->name('brands');
    });
