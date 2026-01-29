<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\Api\SettingController;

Route::prefix('v1/settings')->name('settings.')->group(function () {
    Route::get('/', [SettingController::class, 'show'])->name('show');
});
