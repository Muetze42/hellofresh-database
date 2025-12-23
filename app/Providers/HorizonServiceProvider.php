<?php

namespace App\Providers;

use App\Models\User as Authenticatable;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use Override;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    #[Override]
    public function boot(): void
    {
        parent::boot();

        // \Laravel\Horizon\Horizon::routeSmsNotificationsTo('15556667777');
        // \Laravel\Horizon\Horizon::routeMailNotificationsTo('example@example.com');
        // \Laravel\Horizon\Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    #[Override]
    protected function gate(): void
    {
        Gate::define('viewHorizon', static function (Authenticatable $user): bool {
            return $user->admin;
        });
    }
}
