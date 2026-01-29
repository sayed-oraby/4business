<?php

use Illuminate\Support\Facades\Route;
use Modules\Post\Http\Controllers\Api\LookupsController;
use Modules\Post\Http\Controllers\Api\PostController;
use Modules\Post\Http\Controllers\Api\PostFavouritesController;
use Modules\Post\Http\Controllers\Api\PostPaymentController;

// Public routes - no authentication required
Route::prefix('v1')->group(function () {
    Route::get('posts', [PostController::class, 'index']);  // List posts
    
    Route::get('lookups/post-types', [LookupsController::class, 'postTypes']);
    Route::get('lookups/packages', [LookupsController::class, 'packages']);
    Route::get('lookups/skills', [LookupsController::class, 'skills']);
});

// Protected routes - authentication required
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
   
    // IMPORTANT: Specific routes must come BEFORE wildcard routes
    Route::get('posts/my-posts', [PostController::class, 'myPosts']);  // My posts list
    Route::get('posts/my-posts/{post}', [PostController::class, 'myPost']);  // Single my-post details
    Route::post('posts', [PostController::class, 'store']);  // Create post
    Route::put('posts/{post}', [PostController::class, 'update']);  // Update post
    Route::delete('posts/{post}', [PostController::class, 'destroy']);  // Delete post
    
    Route::post('posts/{post}/stop', [PostController::class, 'stop']);
    Route::post('posts/{post}/resume', [PostController::class, 'resume']);
    
    Route::post('posts/{post}/job-offers', [\Modules\Post\Http\Controllers\Api\JobOfferController::class, 'store']);

    Route::get('favourites', [PostFavouritesController::class, 'list']);  // Single my-post details
    Route::post('favourites', [PostFavouritesController::class, 'store']);  // Single my-post details
    
    

});

// Payment routes
Route::prefix('v1')->group(function () {
    // Payment callback route (public - accessed by payment gateway)
    Route::any('posts/{post}/payment/callback', [PostPaymentController::class, 'callback'])
        ->name('posts.payment.callback');

    // Payment retry route (protected - requires authentication)
    Route::post('posts/{post}/payment/retry', [PostPaymentController::class, 'retry'])
        ->middleware('auth:sanctum')
        ->name('posts.payment.retry');
});

// Public single post route - MUST come after protected specific routes to avoid conflict
Route::prefix('v1')->group(function () {
    Route::get('posts/{post}', [PostController::class, 'show'])->middleware('optional.auth');  // Get post by ID
});