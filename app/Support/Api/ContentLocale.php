<?php

namespace App\Support\Api;

/**
 * Helper class to retrieve the content locale for API requests.
 *
 * The content locale is used for translatable content (recipes, ingredients, etc.)
 * without affecting the application locale (which stays in English for error messages).
 */
final class ContentLocale
{
    /**
     * Get the current content locale from the container.
     *
     * Falls back to the application's default locale if not set.
     */
    public static function get(): string
    {
        /** @var string|null $locale */
        $locale = app()->bound('current.content_locale')
            ? resolve('current.content_locale')
            : null;

        return $locale ?? config('app.locale', 'en');
    }
}
