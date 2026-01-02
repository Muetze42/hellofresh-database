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
            fn (string $value): string => Str::ucfirst(Str::squish(Str::before($value, ' (')))
        );

        Str::macro(
            'countryFlag',
            function (string $countryCode): string {
                $code = Str::upper($countryCode);
                $flag = '';
                for ($i = 0; $i < strlen($code); $i++) {
                    $flag .= mb_chr(ord($code[$i]) - ord('A') + 0x1F1E6);
                }

                return $flag;
            }
        );
    }
}
