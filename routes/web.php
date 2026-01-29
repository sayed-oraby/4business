<?php

use Illuminate\Support\Facades\Route;

// Home page is handled by Core module (Modules/Core/routes/web.php)

Route::get('/_boost/browser-logs', function () {
    return response()->noContent();
});
