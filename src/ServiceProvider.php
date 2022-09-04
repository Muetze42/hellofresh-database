<?php

namespace NormanHuth\HelloFreshDatabase;

use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadJSONTranslationsFrom(__DIR__.'/../lang');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }
}
