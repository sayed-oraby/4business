<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\Api\OrderController;
use Modules\Order\Http\Controllers\Api\PaymentCallbackController;

Route::prefix('v1')->name('api.orders.')->group(function () {
    Route::post('orders', [OrderController::class, 'store'])->name('store');
    Route::get('orders/{order}', [OrderController::class, 'show'])->middleware('auth:sanctum')->name('show');

    Route::post('payments/sadad/callback', [PaymentCallbackController::class, 'webhook'])->name('payments.sadad.callback');
    Route::get('payments/sadad/result', [PaymentCallbackController::class, 'result'])->name('payments.sadad.result');
    Route::get('payments/sadad/result/{invoiceId}/{status?}', [PaymentCallbackController::class, 'result'])->name('payments.sadad.result.path');
});
