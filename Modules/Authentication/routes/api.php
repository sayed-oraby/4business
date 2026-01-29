<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\Http\Controllers\Api\AuthController;

Route::prefix('v1/auth')->group(function () {

    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('verify-account', [AuthController::class, 'verifyAccount'])->name('auth.verify-account');
    Route::post('resend-otp', [AuthController::class, 'resendOtp'])->name('auth.resend-otp');


    Route::post('password/forgot', [AuthController::class, 'forgotPassword'])->name('auth.password.forgot');
    Route::post('password/verify', [AuthController::class, 'verifyPasswordOtp'])->name('auth.password.verify');
    Route::post('password/reset', [AuthController::class, 'resetPassword'])->name('auth.password.reset');
});
