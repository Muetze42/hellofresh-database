<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureStrMacros();
    }

    /**
     * Configure custom string macros.
     *
     * @noinspection StaticClosureCanBeUsedInspection
     */
    protected function configureStrMacros(): void
    {
        Str::macro(
            'normalizeName',
            fn (string $value): string => Str::ucfirst(Str::squish($value))
        );

        Str::macro(
            'normalizeNameStrict',
            fn (string $value): string => Str::ucfirst(Str::squish(rtrim(Str::before($value, ' ('), '*')))
        );
    }
}
