<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LivewireServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Livewire::addPersistentMiddleware(LivewireSentryContextMiddleware::class);
    }
}
