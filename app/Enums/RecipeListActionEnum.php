<?php

namespace App\Enums;

enum RecipeListActionEnum: string
{
    case Added = 'added';
    case Removed = 'removed';

    /**
     * Get the translated label for this action.
     */
    public function label(): string
    {
        return match ($this) {
            self::Added => __('added'),
            self::Removed => __('removed'),
        };
    }
}
