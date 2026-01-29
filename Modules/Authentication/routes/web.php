<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\Http\Controllers\Admin\AuthenticatedSessionController;
use Modules\Authentication\Http\Controllers\Admin\PasswordResetController;
use Modules\Authentication\Http\Controllers\Frontend\AuthController;

/*
|--------------------------------------------------------------------------
| Frontend Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('web')
    ->prefix('auth')
    ->name('frontend.')
    ->group(function () {
        Route::middleware('guest:admin')->group(function () {
            Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
            Route::post('/login', [AuthController::class, 'login'])->name('login.post');
            Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
            Route::post('/register', [AuthController::class, 'register'])->name('register.post');
            Route::get('/otp', [AuthController::class, 'showOtp'])->name('otp');
            Route::post('/otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');
            Route::get('/otp/resend', [AuthController::class, 'resendOtp'])->name('otp.resend');

            // Password Reset Routes
            Route::get('/forgot-password', [AuthController::class, 'showPasswordRequest'])->name('password.request');
            Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetOtp'])->name('password.send');
            Route::get('/password/otp', [AuthController::class, 'showPasswordOtp'])->name('password.otp');
            Route::post('/password/otp', [AuthController::class, 'verifyPasswordOtp'])->name('password.otp.verify');
            Route::post('/password/otp/resend', [AuthController::class, 'resendPasswordOtp'])->name('password.otp.resend');
            Route::get('/reset-password', [AuthController::class, 'showPasswordReset'])->name('password.reset');
            Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

            Route::get('/social/{provider}', [AuthController::class, 'socialRedirect'])->name('social.redirect');
            Route::get('/social/{provider}/callback', [AuthController::class, 'socialCallback'])->name('social.callback');
        });

        Route::middleware('auth:admin')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        });
    });

/*
|--------------------------------------------------------------------------
| Dashboard Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('web')
    ->get('/login', fn () => redirect()->route('dashboard.login'))
    ->name('login');

Route::middleware('web')
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        Route::middleware('guest:admin')->group(function () {
            Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
            Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
            Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
            Route::post('/forgot-password', [PasswordResetController::class, 'sendOtp'])->name('password.email');
            Route::get('/password/otp', [PasswordResetController::class, 'showOtpForm'])->name('password.otp');
            Route::post('/password/otp', [PasswordResetController::class, 'verifyOtp'])->name('password.otp.verify');
            Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
            Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
        });

        Route::middleware('auth:admin')->group(function () {
            Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        });
    });
