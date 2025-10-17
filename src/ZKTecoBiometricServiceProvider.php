<?php

namespace AhidTechnologies\ZKTecoBiometric;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use AhidTechnologies\ZKTecoBiometric\Services\AttendanceProcessor;

class ZKTecoBiometricServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/zkteco-biometric.php', 'zkteco-biometric');

        // Register services
        $this->app->singleton(AttendanceProcessor::class, function ($app) {
            return new AttendanceProcessor();
        });

        // Register facade
        $this->app->bind('zkteco-biometric', function ($app) {
            return new ZKTecoBiometric();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Register routes
        $this->registerRoutes();

        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/zkteco-biometric.php' => config_path('zkteco-biometric.php'),
        ], 'config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        // Prepare middleware array
        $middleware = config('zkteco-biometric.middleware', []);

        // Add logging middleware if logging is enabled and respects debug mode
        if ($this->shouldEnableLogging()) {
            $middleware[] = \AhidTechnologies\ZKTecoBiometric\Http\Middleware\LogZKTecoRequests::class;
        }

        Route::group([
            'prefix' => config('zkteco-biometric.route_prefix', ''),
            'middleware' => $middleware,
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        });
    }

    /**
     * Determine if logging should be enabled based on configuration
     */
    protected function shouldEnableLogging(): bool
    {
        $loggingEnabled = config('zkteco-biometric.logging.enabled', true);
        $respectAppDebug = config('zkteco-biometric.logging.respect_app_debug', true);

        if ($respectAppDebug) {
            return $loggingEnabled && config('app.debug', false);
        }

        return $loggingEnabled;
    }
}
