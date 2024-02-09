<?php

use App\Models\Country;
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
        return Str::slug($title, $separator, app()->getLocale());
    }
}
