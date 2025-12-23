<?php

namespace App\Support\HelloFresh;

class HelloFreshAsset
{
    /**
     * Generate an asset URL for a HelloFresh cloud media.
     */
    public static function url(?string $imagePath, string $transformation): ?string
    {
        if ($imagePath === null || $imagePath === '') {
            return null;
        }

        $baseUrl = config('hellofresh.cdn.base_url');
        $bucket = config('hellofresh.cdn.bucket');

        return $baseUrl . '/' . $transformation . '/' . $bucket . $imagePath;
    }

    /**
     * Generate a recipe card image URL.
     */
    public static function recipeCard(?string $imagePath): ?string
    {
        return self::url($imagePath, config('hellofresh.assets.recipe.card'));
    }

    /**
     * Generate a recipe header image URL.
     */
    public static function recipeHeader(?string $imagePath): ?string
    {
        return self::url($imagePath, config('hellofresh.assets.recipe.header'));
    }

    /**
     * Generate an ingredient thumbnail URL.
     */
    public static function ingredientThumbnail(?string $imagePath): ?string
    {
        return self::url($imagePath, config('hellofresh.assets.ingredient.thumbnail'));
    }

    /**
     * Generate a step image URL.
     */
    public static function stepImage(?string $imagePath): ?string
    {
        return self::url($imagePath, config('hellofresh.assets.step.image'));
    }
}
