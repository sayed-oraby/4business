<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\ProfileController;

Route::middleware(['auth:sanctum'])->prefix('v1/profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
});
