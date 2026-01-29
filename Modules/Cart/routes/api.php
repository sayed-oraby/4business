<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\Api\CartController;
use Modules\Cart\Http\Controllers\Api\WishlistController;
use Modules\Cart\Http\Controllers\Api\ShippingEstimateController;

Route::prefix('v1')
    ->name('cart.')
    ->group(function () {
        Route::get('cart', [CartController::class, 'show'])->name('show');
        Route::post('cart/items', [CartController::class, 'addItem'])->name('items.add');
        Route::put('cart/items/{item}', [CartController::class, 'updateItem'])->name('items.update');
        Route::delete('cart/items/{item}', [CartController::class, 'removeItem'])->name('items.remove');
        Route::post('cart/refresh', [CartController::class, 'refresh'])->name('refresh');
        Route::post('cart/validate-checkout', [CartController::class, 'validateCheckout'])->name('validate');
        Route::middleware('auth:sanctum')->post('cart/shipping-estimate', ShippingEstimateController::class)->name('shipping-estimate');

        Route::get('wishlist', [WishlistController::class, 'show'])->name('wishlist.show');
        Route::post('wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    });


    
