<?php

namespace Modules\Order\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('web')
                ->namespace('Modules\\Order\\Http\\Controllers')
                ->group(module_path('Order', '/routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace('Modules\\Order\\Http\\Controllers')
                ->group(module_path('Order', '/routes/api.php'));
        });
    }
}
