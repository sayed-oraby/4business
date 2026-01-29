<?php

use Illuminate\Support\Facades\Route;
use Modules\Reports\Http\Controllers\Dashboard\CouponsReportController;
use Modules\Reports\Http\Controllers\Dashboard\CartsReportController;
use Modules\Reports\Http\Controllers\Dashboard\FinancialReportController;
use Modules\Reports\Http\Controllers\Dashboard\OrdersReportController;
use Modules\Reports\Http\Controllers\Dashboard\ProductsReportController;
use Modules\Reports\Http\Controllers\Dashboard\ReportsController;
use Modules\Reports\Http\Controllers\Dashboard\SalesReportController;
use Modules\Reports\Http\Controllers\Dashboard\SecurityReportController;
use Modules\Reports\Http\Controllers\Dashboard\UsersReportController;

Route::middleware(['web', 'auth:admin'])
    ->prefix('dashboard/reports')
    ->name('dashboard.reports.')
    ->group(function () {
        // Main Reports Dashboard
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/stats', [ReportsController::class, 'stats'])->name('stats');

        // Sales Reports
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/', [SalesReportController::class, 'index'])->name('index');
            Route::get('/stats', [SalesReportController::class, 'stats'])->name('stats');
            Route::get('/by-product', [SalesReportController::class, 'byProduct'])->name('by-product');
            Route::get('/by-country', [SalesReportController::class, 'byCountry'])->name('by-country');
            Route::get('/by-payment-method', [SalesReportController::class, 'byPaymentMethod'])->name('by-payment-method');
            Route::get('/by-time', [SalesReportController::class, 'byTime'])->name('by-time');
        });

        // Orders Reports
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrdersReportController::class, 'index'])->name('index');
            Route::get('/by-status', [OrdersReportController::class, 'byStatus'])->name('by-status');
            Route::get('/by-payment-status', [OrdersReportController::class, 'byPaymentStatus'])->name('by-payment-status');
            Route::get('/payment-failures', [OrdersReportController::class, 'paymentFailures'])->name('payment-failures');
            Route::get('/details', [OrdersReportController::class, 'details'])->name('details');
        });

        // Carts Reports
        Route::prefix('carts')->name('carts.')->group(function () {
            Route::get('/', [CartsReportController::class, 'index'])->name('index');
            Route::post('/notify', [CartsReportController::class, 'notify'])->name('notify');
        });

        // Products Reports
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductsReportController::class, 'index'])->name('index');
            Route::get('/inventory', [ProductsReportController::class, 'inventory'])->name('inventory');
            Route::get('/pricing-by-country', [ProductsReportController::class, 'pricingByCountry'])->name('pricing-by-country');
            Route::get('/price-changes', [ProductsReportController::class, 'priceChanges'])->name('price-changes');
        });

        // Users Reports
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UsersReportController::class, 'index'])->name('index');
            Route::get('/signups', [UsersReportController::class, 'signups'])->name('signups');
            Route::get('/top-buyers', [UsersReportController::class, 'topBuyers'])->name('top-buyers');
            Route::get('/behavior', [UsersReportController::class, 'behavior'])->name('behavior');
        });

        // Coupons Reports
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [CouponsReportController::class, 'index'])->name('index');
            Route::get('/stats', [CouponsReportController::class, 'stats'])->name('stats');
            Route::get('/by-user/{user}', [CouponsReportController::class, 'byUser'])->name('by-user');
            Route::get('/by-product', [CouponsReportController::class, 'byProduct'])->name('by-product');
        });

        // Financial Reports
        Route::prefix('financial')->name('financial.')->group(function () {
            Route::get('/', [FinancialReportController::class, 'index'])->name('index');
            Route::get('/revenue', [FinancialReportController::class, 'revenue'])->name('revenue');
            Route::get('/tax', [FinancialReportController::class, 'tax'])->name('tax');
            Route::get('/wallet', [FinancialReportController::class, 'wallet'])->name('wallet');
        });

        // Security Reports
        Route::prefix('security')->name('security.')->group(function () {
            Route::get('/', [SecurityReportController::class, 'index'])->name('index');
            Route::get('/login-attempts', [SecurityReportController::class, 'loginAttempts'])->name('login-attempts');
            Route::get('/bans', [SecurityReportController::class, 'bans'])->name('bans');
            Route::get('/audit-log', [SecurityReportController::class, 'auditLog'])->name('audit-log');
            Route::get('/admin-actions', [SecurityReportController::class, 'adminActions'])->name('admin-actions');
        });
        
        // === NEW JOB POSTING REPORTS ===
        Route::get('/posts', [ReportsController::class, 'posts'])->name('posts');
        Route::get('/job-offers', [ReportsController::class, 'jobOffers'])->name('job-offers');
        Route::get('/members', [ReportsController::class, 'users'])->name('members');
        Route::get('/revenue', [ReportsController::class, 'financial'])->name('revenue');
    });
