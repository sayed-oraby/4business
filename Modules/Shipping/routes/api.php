<?php

use Illuminate\Support\Facades\Route;
use Modules\Shipping\Http\Controllers\Api\AddressController;
use Modules\Shipping\Http\Controllers\Api\CountryController;

Route::prefix('v1')->name('api.shipping.')->group(function () {
    Route::get('shipping/countries', [CountryController::class, 'index'])
        ->name('countries.index');
    Route::get('shipping/countries/package', [CountryController::class, 'package'])
        ->name('countries.package');
    Route::get('shipping/states', [CountryController::class, 'states'])
        ->name('states');
    Route::get('shipping/cities', [CountryController::class, 'cities'])
        ->name('cities');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('shipping/addresses', [AddressController::class, 'index']);
        Route::post('shipping/addresses', [AddressController::class, 'store']);
        Route::put('shipping/addresses/{address}', [AddressController::class, 'update']);
        Route::delete('shipping/addresses/{address}', [AddressController::class, 'destroy']);
    });
});
