<?php

use Illuminate\Support\Facades\Route;
use Modules\Shipping\Http\Controllers\Dashboard\CityController;
use Modules\Shipping\Http\Controllers\Dashboard\CountryController;
use Modules\Shipping\Http\Controllers\Dashboard\RateController;
use Modules\Shipping\Http\Controllers\Dashboard\StateController;

Route::middleware(['web', 'auth:admin'])
    ->prefix('dashboard/shipping/countries')
    ->name('dashboard.shipping.countries.')
    ->group(function () {
        Route::get('/', [CountryController::class, 'index'])
            ->middleware('permission:shipping_countries.view')
            ->name('index');

        Route::get('/data', [CountryController::class, 'data'])
            ->middleware('permission:shipping_countries.view')
            ->name('data');

        Route::post('/', [CountryController::class, 'store'])
            ->middleware('permission:shipping_countries.create')
            ->name('store');

        Route::put('/{country}', [CountryController::class, 'update'])
            ->middleware('permission:shipping_countries.update')
            ->name('update');

        Route::delete('/{country}', [CountryController::class, 'destroy'])
            ->middleware('permission:shipping_countries.delete')
            ->name('destroy');

        Route::get('/{country}/rates', [RateController::class, 'index'])
            ->middleware('permission:shipping_countries.view')
            ->name('rates.index');

        Route::post('/{country}/rates', [RateController::class, 'store'])
            ->middleware('permission:shipping_countries.update')
            ->name('rates.store');

        Route::put('/rates/{rate}', [RateController::class, 'update'])
            ->middleware('permission:shipping_countries.update')
            ->name('rates.update');

        Route::delete('/rates/{rate}', [RateController::class, 'destroy'])
            ->middleware('permission:shipping_countries.update')
            ->name('rates.destroy');

        Route::post('/{country}/import-locations', [CountryController::class, 'importLocations'])
            ->middleware('permission:shipping_countries.update')
            ->name('import');

        Route::get('/{country}/states/list', [CountryController::class, 'states'])
            ->middleware('permission:shipping_countries.view')
            ->name('states');
    });

Route::middleware(['web', 'auth:admin'])
    ->prefix('dashboard/shipping/locations')
    ->name('dashboard.shipping.locations.')
    ->group(function () {
        Route::get('/', [StateController::class, 'index'])
            ->middleware('permission:shipping_countries.view')
            ->name('index');

        Route::get('/states', [StateController::class, 'data'])
            ->middleware('permission:shipping_countries.view')
            ->name('states');

        Route::post('/states', [StateController::class, 'store'])
            ->middleware('permission:shipping_countries.update')
            ->name('states.store');

        Route::put('/states/{state}', [StateController::class, 'update'])
            ->middleware('permission:shipping_countries.update')
            ->name('states.update');

        Route::delete('/states/{state}', [StateController::class, 'destroy'])
            ->middleware('permission:shipping_countries.delete')
            ->name('states.destroy');

        Route::get('/cities', [CityController::class, 'data'])
            ->middleware('permission:shipping_countries.view')
            ->name('cities');

        Route::post('/cities', [CityController::class, 'store'])
            ->middleware('permission:shipping_countries.update')
            ->name('cities.store');

        Route::put('/cities/{city}', [CityController::class, 'update'])
            ->middleware('permission:shipping_countries.update')
            ->name('cities.update');

        Route::delete('/cities/{city}', [CityController::class, 'destroy'])
            ->middleware('permission:shipping_countries.delete')
            ->name('cities.destroy');
    });
