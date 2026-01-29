<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Dashboard\UserController;
use Modules\User\Http\Controllers\Frontend\AccountController;
use Modules\User\Http\Controllers\Frontend\AgentController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/
Route::middleware('web')
    ->name('frontend.')
    ->group(function () {
        // Agents/Offices
        Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
        Route::get('/agents/load-more/{agentId}', [AgentController::class, 'loadMore'])->name('agents.loadMore');
        Route::get('/agents/{slug}', [AgentController::class, 'show'])->name('agents.show');

        // User Account
        Route::middleware('auth:admin')->prefix('account')->name('account.')->group(function () {
            Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
            Route::get('/edit', [AccountController::class, 'edit'])->name('edit');
            Route::put('/edit', [AccountController::class, 'update'])->name('update');
            Route::get('/password', [AccountController::class, 'password'])->name('password');
            Route::put('/password', [AccountController::class, 'updatePassword'])->name('password.update');
            Route::get('/become-agent', [AccountController::class, 'becomeAgent'])->name('become-agent');
            Route::post('/become-agent', [AccountController::class, 'storeBecomeAgent'])->name('become-agent.store');
        });
    });

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth:admin', 'permission:dashboard.access'])
    ->prefix('dashboard/users')
    ->name('dashboard.users.')
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/data', [UserController::class, 'data'])->name('data');

        Route::get('/office-requests', [UserController::class, 'officeRequests'])->name('office-requests.index');
        Route::get('/office-requests/data', [UserController::class, 'officeRequestsData'])->name('office-requests.data');
        Route::put('/office-requests/{user}/status', [UserController::class, 'updateOfficeRequestStatus'])->name('office-requests.update-status');
        Route::delete('/office-requests/{user}', [UserController::class, 'destroyOfficeRequest'])->name('office-requests.destroy');

        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

        Route::post('/bulk-delete', [UserController::class, 'bulkDestroy'])->name('bulk-delete');
        Route::post('/{user}/restore', [UserController::class, 'restore'])->name('restore');
    });
