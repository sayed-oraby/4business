<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\Dashboard\OrderController;
use Modules\Order\Http\Controllers\Dashboard\StatusController;

Route::middleware(['web', 'auth:admin'])
    ->prefix('dashboard/orders')
    ->name('dashboard.orders.')
    ->group(function () {
        Route::get('/', [OrderController::class, 'index'])->middleware('permission:orders.view')->name('index');
        Route::get('/data', [OrderController::class, 'data'])->middleware('permission:orders.view')->name('data');
        Route::get('/{order}', [OrderController::class, 'show'])->middleware('permission:orders.view')->name('show');
        Route::get('/{order}/edit', [OrderController::class, 'edit'])->middleware('permission:orders.update')->name('edit');
        Route::put('/{order}', [OrderController::class, 'update'])->middleware('permission:orders.update')->name('update');
        Route::post('/{order}/status', [OrderController::class, 'changeStatus'])->middleware('permission:orders.update')->name('status');
        Route::post('/{order}/payment-status', [OrderController::class, 'changePaymentStatus'])->middleware('permission:orders.update')->name('payment-status');
    });

Route::middleware(['web', 'auth:admin'])
    ->prefix('dashboard/order-statuses')
    ->name('dashboard.order-statuses.')
    ->group(function () {
        Route::get('/', [StatusController::class, 'index'])->middleware('permission:order_statuses.view')->name('index');
        Route::get('/data', [StatusController::class, 'data'])->middleware('permission:order_statuses.view')->name('data');
        Route::post('/', [StatusController::class, 'store'])->middleware('permission:order_statuses.create')->name('store');
        Route::put('/{status}', [StatusController::class, 'update'])->middleware('permission:order_statuses.update')->name('update');
        Route::delete('/{status}', [StatusController::class, 'destroy'])->middleware('permission:order_statuses.delete')->name('destroy');
    });
