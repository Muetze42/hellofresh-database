<?php

namespace App\Enums;

enum FilterSharePageEnum: string
{
    case Recipes = 'recipes';
    case Random = 'random';
    case Menus = 'menus';

    /**
     * Get the route name for this page.
     */
    public function routeName(): string
    {
        return match ($this) {
            self::Recipes => 'localized.recipes.index',
            self::Random => 'localized.recipes.random',
            self::Menus => 'localized.menus.index',
        };
    }
}
