<?php

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

if (! function_exists('current_country')) {
    /**
     * Get the current country from the container.
     */
    function current_country(): Country
    {
        if (! app()->bound('current.country')) {
            throw new RuntimeException('The current country is not set. Ensure CountryMiddleware has run.');
        }

        return resolve('current.country');
    }
}

if (! function_exists('localized_route')) {
    /**
     * Generate the URL to a localized named route.
     */
    function localized_route(
        string $name,
        mixed $parameters = [],
        bool $absolute = true,
        ?Country $country = null,
        ?string $locale = null
    ): string {
        $country ??= current_country();

        if (! $locale) {
            $locale = app()->getLocale();
        }

        $parameters = array_merge([
            'country' => $country->code,
            'locale' => $locale,
        ], (array) $parameters);

        return route($name, $parameters, $absolute);
    }
}

if (! function_exists('page_title')) {
    /**
     * Build a page title from breadcrumbs with app name suffix.
     */
    function page_title(int|float|string|Model|null ...$crumbs): string
    {
        $appName = __(config('app.name'));
        if (! app()->isProduction()) {
            $appName .= ' [' . app()->environment() . ']';
        }

        $crumbs[] = $appName;

        $crumbs = array_map(static function (mixed $crumb) {
            if ($crumb instanceof Model) {
                $crumb = $crumb->hasAttribute('name') ? $crumb->getAttribute('name') : $crumb->getKey();
            }

            return Str::squish((string) $crumb);
        }, $crumbs);
        $crumbs = array_filter($crumbs);

        $separator = Config::string('project.page_title_separator', ' · ');

        return implode($separator, $crumbs);
    }
}
