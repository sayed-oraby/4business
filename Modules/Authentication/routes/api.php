<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\Http\Controllers\Api\AuthController;
use Modules\Authentication\Http\Controllers\Api\LoginByMobileController;

Route::prefix('v1/auth')->group(function () {

    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('verify-account', [AuthController::class, 'verifyAccount'])->name('auth.verify-account');
    Route::post('resend-otp', [AuthController::class, 'resendOtp'])->name('auth.resend-otp');


    Route::post('password/forgot', [AuthController::class, 'forgotPassword'])->name('auth.password.forgot');
    Route::post('password/verify', [AuthController::class, 'verifyPasswordOtp'])->name('auth.password.verify');
    Route::post('password/reset', [AuthController::class, 'resetPassword'])->name('auth.password.reset');

    // Mobile-only login/register with OTP
    Route::prefix('mobile-login')->name('auth.mobile.')->group(function () {
        Route::post('login', [LoginByMobileController::class, 'loginWithMobile'])->name('login-with-mobile');
        Route::post('verify-otp', [LoginByMobileController::class, 'verifyOtp'])->name('verify-otp');
        Route::post('resend-otp', [LoginByMobileController::class, 'resendOtp'])->name('resend-otp');
    });
});
