<?php

use App\Models\Country;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

if (!function_exists('country')) {
    /**
     * Get the available Country instance.
     */
    function country(): ?Country
    {
        return App::country();
    }
}

if (!function_exists('strSlug')) {
    /**
     * Generate a localized URL friendly 'slug' from a given string.
     */
    function strSlug(string $title, string $separator = '-'): string
    {
        $language = app()->getLocale();
        if (!in_array($language, ['en', 'de', 'bg'])) {
            $language = 'en';
        }

        return Str::slug($title, $separator, $language);
    }
}

if (!function_exists('iso8601ToMinutes')) {
    /**
     * Convert an ISO 8601 duration string to minutes.
     */
    function iso8601ToMinutes(string $iso8601): int
    {
        return (CarbonInterval::make($iso8601))->totalMinutes;
    }
}
