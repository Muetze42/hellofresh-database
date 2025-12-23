<?php

namespace App\Enums;

enum DifficultyEnum: int
{
    case Easy = 1;
    case Medium = 2;
    case Hard = 3;

    public function label(): string
    {
        return match ($this) {
            self::Easy => __('Easy'),
            self::Medium => __('Medium'),
            self::Hard => __('Hard'),
        };
    }
}
