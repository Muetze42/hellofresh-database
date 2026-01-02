<?php

namespace App\Providers;

use App\Http\Middleware\LivewireContextMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Livewire::setUpdateRoute(static function (array $handle) {
            return Route::post('livewire/update', $handle)
                ->middleware([
                    'web',
                    LivewireContextMiddleware::class,
                ]);
        });
    }
}
