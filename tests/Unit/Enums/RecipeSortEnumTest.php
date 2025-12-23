<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\RecipeSortEnum;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RecipeSortEnumTest extends TestCase
{
    #[Test]
    public function it_has_correct_cases(): void
    {
        $cases = RecipeSortEnum::cases();

        $this->assertCount(10, $cases);
    }

    #[Test]
    #[DataProvider('valueProvider')]
    public function it_has_correct_values(RecipeSortEnum $sort, string $expectedValue): void
    {
        $this->assertSame($expectedValue, $sort->value);
    }

    /**
     * @return Iterator<string, array{RecipeSortEnum, string}>
     */
    public static function valueProvider(): Iterator
    {
        yield 'newest_first' => [RecipeSortEnum::NewestFirst, 'hellofresh_created_at-desc'];
        yield 'oldest_first' => [RecipeSortEnum::OldestFirst, 'hellofresh_created_at-asc'];
        yield 'recently_updated' => [RecipeSortEnum::RecentlyUpdated, 'hellofresh_updated_at-desc'];
        yield 'least_recently_updated' => [RecipeSortEnum::LeastRecentlyUpdated, 'hellofresh_updated_at-asc'];
        yield 'prep_time_asc' => [RecipeSortEnum::PrepTimeAsc, 'prep_time-asc'];
        yield 'prep_time_desc' => [RecipeSortEnum::PrepTimeDesc, 'prep_time-desc'];
        yield 'total_time_asc' => [RecipeSortEnum::TotalTimeAsc, 'total_time-asc'];
        yield 'total_time_desc' => [RecipeSortEnum::TotalTimeDesc, 'total_time-desc'];
        yield 'difficulty_asc' => [RecipeSortEnum::DifficultyAsc, 'difficulty-asc'];
        yield 'difficulty_desc' => [RecipeSortEnum::DifficultyDesc, 'difficulty-desc'];
    }

    #[Test]
    #[DataProvider('labelProvider')]
    public function it_returns_correct_label(RecipeSortEnum $sort, string $expectedLabel): void
    {
        $this->assertSame($expectedLabel, $sort->label());
    }

    /**
     * @return Iterator<string, array{RecipeSortEnum, string}>
     */
    public static function labelProvider(): Iterator
    {
        yield 'newest_first' => [RecipeSortEnum::NewestFirst, 'Newest first'];
        yield 'oldest_first' => [RecipeSortEnum::OldestFirst, 'Oldest first'];
        yield 'recently_updated' => [RecipeSortEnum::RecentlyUpdated, 'Recently updated'];
        yield 'least_recently_updated' => [RecipeSortEnum::LeastRecentlyUpdated, 'Least recently updated'];
        yield 'prep_time_asc' => [RecipeSortEnum::PrepTimeAsc, 'Prep time (shortest)'];
        yield 'prep_time_desc' => [RecipeSortEnum::PrepTimeDesc, 'Prep time (longest)'];
        yield 'total_time_asc' => [RecipeSortEnum::TotalTimeAsc, 'Total time (shortest)'];
        yield 'total_time_desc' => [RecipeSortEnum::TotalTimeDesc, 'Total time (longest)'];
        yield 'difficulty_asc' => [RecipeSortEnum::DifficultyAsc, 'Difficulty (easiest)'];
        yield 'difficulty_desc' => [RecipeSortEnum::DifficultyDesc, 'Difficulty (hardest)'];
    }

    #[Test]
    #[DataProvider('columnProvider')]
    public function it_returns_correct_column(RecipeSortEnum $sort, string $expectedColumn): void
    {
        $this->assertSame($expectedColumn, $sort->column());
    }

    /**
     * @return Iterator<string, array{RecipeSortEnum, string}>
     */
    public static function columnProvider(): Iterator
    {
        yield 'newest_first' => [RecipeSortEnum::NewestFirst, 'hellofresh_created_at'];
        yield 'oldest_first' => [RecipeSortEnum::OldestFirst, 'hellofresh_created_at'];
        yield 'recently_updated' => [RecipeSortEnum::RecentlyUpdated, 'hellofresh_updated_at'];
        yield 'least_recently_updated' => [RecipeSortEnum::LeastRecentlyUpdated, 'hellofresh_updated_at'];
        yield 'prep_time_asc' => [RecipeSortEnum::PrepTimeAsc, 'prep_time'];
        yield 'prep_time_desc' => [RecipeSortEnum::PrepTimeDesc, 'prep_time'];
        yield 'total_time_asc' => [RecipeSortEnum::TotalTimeAsc, 'total_time'];
        yield 'total_time_desc' => [RecipeSortEnum::TotalTimeDesc, 'total_time'];
        yield 'difficulty_asc' => [RecipeSortEnum::DifficultyAsc, 'difficulty'];
        yield 'difficulty_desc' => [RecipeSortEnum::DifficultyDesc, 'difficulty'];
    }

    #[Test]
    #[DataProvider('directionProvider')]
    public function it_returns_correct_direction(RecipeSortEnum $sort, string $expectedDirection): void
    {
        $this->assertSame($expectedDirection, $sort->direction());
    }

    /**
     * @return Iterator<string, array{RecipeSortEnum, string}>
     */
    public static function directionProvider(): Iterator
    {
        yield 'newest_first' => [RecipeSortEnum::NewestFirst, 'desc'];
        yield 'oldest_first' => [RecipeSortEnum::OldestFirst, 'asc'];
        yield 'recently_updated' => [RecipeSortEnum::RecentlyUpdated, 'desc'];
        yield 'least_recently_updated' => [RecipeSortEnum::LeastRecentlyUpdated, 'asc'];
        yield 'prep_time_asc' => [RecipeSortEnum::PrepTimeAsc, 'asc'];
        yield 'prep_time_desc' => [RecipeSortEnum::PrepTimeDesc, 'desc'];
        yield 'total_time_asc' => [RecipeSortEnum::TotalTimeAsc, 'asc'];
        yield 'total_time_desc' => [RecipeSortEnum::TotalTimeDesc, 'desc'];
        yield 'difficulty_asc' => [RecipeSortEnum::DifficultyAsc, 'asc'];
        yield 'difficulty_desc' => [RecipeSortEnum::DifficultyDesc, 'desc'];
    }

    #[Test]
    public function it_can_be_created_from_value(): void
    {
        $this->assertSame(RecipeSortEnum::NewestFirst, RecipeSortEnum::from('hellofresh_created_at-desc'));
        $this->assertSame(RecipeSortEnum::OldestFirst, RecipeSortEnum::from('hellofresh_created_at-asc'));
    }

    #[Test]
    public function it_can_try_from_value(): void
    {
        $this->assertSame(RecipeSortEnum::NewestFirst, RecipeSortEnum::tryFrom('hellofresh_created_at-desc'));
        $this->assertNull(RecipeSortEnum::tryFrom('invalid'));
    }
}
