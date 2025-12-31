<?php

namespace App\Providers;

use App\Http\Middleware\LivewireSentryContextMiddleware;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
