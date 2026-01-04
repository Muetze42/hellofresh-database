<?php

namespace App\Providers;

use App\Models\User as Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PulseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('viewPulse', static fn (Authenticatable $user): bool => $user->admin);
    }
}
