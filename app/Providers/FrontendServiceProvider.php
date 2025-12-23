<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class FrontendServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        $this->configureUrlGenerator();
        $this->configureVitePrefetchingStrategy();
    }

    /**
     * Configure the application's URL Generator.
     */
    protected function configureUrlGenerator(): void
    {
        if (! $this->app->isLocal()) {
            URL::forceScheme('https');
        }
    }

    /**
     * Configure the application's Vite prefetching strategy.
     *
     * @see https://github.com/laravel/framework/pull/52462
     */
    protected function configureVitePrefetchingStrategy(): void
    {
        // Vite::useWaterfallPrefetching(10);
        Vite::useAggressivePrefetching();
    }
}
