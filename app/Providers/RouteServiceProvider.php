<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::prefix('api/user/auth')
                ->middleware(['api'])
                ->namespace($this->namespace)
                ->group(base_path('routes/user/auth.php'));

            Route::prefix('api/app')
                ->middleware(['api'])
                ->namespace($this->namespace)
                ->group(base_path('routes/user/app.php'));

            Route::prefix('api/user')
                ->middleware(['api', 'auth:sanctum'])
                ->namespace($this->namespace)
                ->group(base_path('routes/user/user.php'));

            Route::prefix('api/user/message')
                ->middleware(['api', 'auth:sanctum'])
                ->namespace($this->namespace)
                ->group(base_path('routes/user/chat.php'));

            Route::prefix('api/user/group')
                ->middleware(['api', 'auth:sanctum'])
                ->namespace($this->namespace)
                ->group(base_path('routes/user/group.php'));

            Route::prefix('api/user/post')
                ->middleware(['api', 'auth:sanctum'])
                ->namespace($this->namespace)
                ->group(base_path('routes/user/post.php'));

            Route::prefix('api/community')
                ->middleware(['api', 'auth:sanctum'])
                ->namespace($this->namespace)
                ->group(base_path('routes/user/community.php'));

            Route::prefix('api/web')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/web/web-api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('general/dashboard')
                ->middleware(['web', 'auth'])
                ->group(base_path('routes/general-dashboard.php'));
        });
    }
}
