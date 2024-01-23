<?php

namespace Shahzadthathal\Skipcash\Providers;

use Illuminate\Support\ServiceProvider;

class SkipcashProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->publishes([
            __DIR__ . '/../../config/skipcash.php' => config_path('skipcash.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../../routes' => base_path('routes'),
        ], 'routes');
    }
}