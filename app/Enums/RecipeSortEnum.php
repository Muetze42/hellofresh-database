<?php

namespace App\Enums;

enum RecipeSortEnum: string
{
    case NewestFirst = 'hellofresh_created_at-desc';
    case OldestFirst = 'hellofresh_created_at-asc';
    case RecentlyUpdated = 'hellofresh_updated_at-desc';
    case LeastRecentlyUpdated = 'hellofresh_updated_at-asc';
    case PrepTimeAsc = 'prep_time-asc';
    case PrepTimeDesc = 'prep_time-desc';
    case TotalTimeAsc = 'total_time-asc';
    case TotalTimeDesc = 'total_time-desc';
    case DifficultyAsc = 'difficulty-asc';
    case DifficultyDesc = 'difficulty-desc';

    public function label(): string
    {
        return match ($this) {
            self::NewestFirst => __('Newest first'),
            self::OldestFirst => __('Oldest first'),
            self::RecentlyUpdated => __('Recently updated'),
            self::LeastRecentlyUpdated => __('Least recently updated'),
            self::PrepTimeAsc => __('Prep time (shortest)'),
            self::PrepTimeDesc => __('Prep time (longest)'),
            self::TotalTimeAsc => __('Total time (shortest)'),
            self::TotalTimeDesc => __('Total time (longest)'),
            self::DifficultyAsc => __('Difficulty (easiest)'),
            self::DifficultyDesc => __('Difficulty (hardest)'),
        };
    }

    public function column(): string
    {
        return match ($this) {
            self::NewestFirst, self::OldestFirst => 'hellofresh_created_at',
            self::RecentlyUpdated, self::LeastRecentlyUpdated => 'hellofresh_updated_at',
            self::PrepTimeAsc, self::PrepTimeDesc => 'prep_time',
            self::TotalTimeAsc, self::TotalTimeDesc => 'total_time',
            self::DifficultyAsc, self::DifficultyDesc => 'difficulty',
        };
    }

    public function direction(): string
    {
        return match ($this) {
            self::NewestFirst, self::RecentlyUpdated, self::PrepTimeDesc, self::TotalTimeDesc, self::DifficultyDesc => 'desc',
            self::OldestFirst, self::LeastRecentlyUpdated, self::PrepTimeAsc, self::TotalTimeAsc, self::DifficultyAsc => 'asc',
        };
    }
}
