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
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // Existing internal API routes (untouched)
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            // External API v1 routes (authenticated, rate-limited, logged)
            Route::prefix('api/v1')
                ->middleware('api_external')
                ->group(base_path('routes/api_v1.php'));

            // FHIR R4 API routes
            Route::prefix('api/fhir')
                ->middleware(['api_external', 'fhir.response'])
                ->group(base_path('routes/fhir.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Per-client rate limiting for external API
        RateLimiter::for('api_external', function (Request $request) {
            $client = $request->attributes->get('api_client');
            $limit = $client ? $client->rate_limit : 30;
            $key = $client ? 'api_client:' . $client->id : 'api_ip:' . $request->ip();
            return Limit::perMinute($limit)->by($key)->response(function () use ($limit) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Rate limit exceeded. Maximum {$limit} requests per minute.",
                    'code' => 'RATE_LIMIT_EXCEEDED',
                ], 429);
            });
        });
    }
}
