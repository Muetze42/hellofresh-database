<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/web/components'), 'web');
        View::addNamespace('web', resource_path('views/web'));

        Blade::anonymousComponentPath(resource_path('views/portal/components'), 'portal');
        View::addNamespace('portal', resource_path('views/portal'));

        View::addNamespace('common', resource_path('views/common'));

        Blade::anonymousComponentPath(
            base_path('vendor/livewire/flux/stubs/resources/views/flux'),
            'flux-vendor'
        );
        Blade::anonymousComponentPath(
            base_path('vendor/livewire/flux-pro/stubs/resources/views/flux'),
            'flux-vendor'
        );
    }
}
