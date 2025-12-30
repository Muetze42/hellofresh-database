<?php

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * When to split this file?
 *
 * As soon as one of the following applies:
 *
 * - The file grows beyond ~150 lines.
 * - You have 5 or more functions related to the same topic.
 * - You want to test or document certain functions more specifically.
 * - You prefer to organize functions by type, e.g., string.php for all string-related helpers.
 */
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

if (! function_exists('slugify')) {
    /**
     * Generate a URL friendly 'slug' from a given string.
     */
    function slugify(string $title): string
    {
        return Str::slug($title, language: app()->getLocale());
    }
}

if (! function_exists('validated_per_page')) {
    /**
     * Retrieves the number of items per a page for pagination.
     *
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    function validated_per_page(?Request $request = null, int $default = 100, int $max = 1_000, int $min = 10): int
    {
        if (! $request instanceof Request) {
            $request = resolve('request');
        }

        $perPage = $request->integer('perPage', $default);

        if ($perPage >= $min && $perPage <= $max) {
            return $perPage;
        }

        return nearest_allowed($perPage, [$min, $max]);
    }
}

if (! function_exists('nearest_allowed')) {
    /**
     * Determine the nearest allowed value from a list of allowed values.
     *
     * @param  int[]  $allowed
     */
    function nearest_allowed(int $value, array $allowed): int
    {
        sort($allowed);

        if (in_array($value, $allowed, true)) {
            return $value;
        }

        if ($value < $allowed[0]) {
            return $allowed[0];
        }

        if ($value > end($allowed)) {
            return end($allowed);
        }

        foreach ($allowed as $key => $option) {
            if ($option > $value) {
                return $allowed[$key - 1];
            }
        }

        return end($allowed);
    }
}
