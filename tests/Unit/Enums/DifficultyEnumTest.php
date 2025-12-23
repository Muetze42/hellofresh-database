<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\DifficultyEnum;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DifficultyEnumTest extends TestCase
{
    #[Test]
    public function it_has_correct_cases(): void
    {
        $cases = DifficultyEnum::cases();

        $this->assertCount(3, $cases);
        $this->assertContains(DifficultyEnum::Easy, $cases);
        $this->assertContains(DifficultyEnum::Medium, $cases);
        $this->assertContains(DifficultyEnum::Hard, $cases);
    }

    #[Test]
    public function it_has_correct_values(): void
    {
        $this->assertSame(1, DifficultyEnum::Easy->value);
        $this->assertSame(2, DifficultyEnum::Medium->value);
        $this->assertSame(3, DifficultyEnum::Hard->value);
    }

    #[Test]
    #[DataProvider('labelProvider')]
    public function it_returns_correct_label(DifficultyEnum $difficulty, string $expectedLabel): void
    {
        $this->assertSame($expectedLabel, $difficulty->label());
    }

    /**
     * @return Iterator<string, array{DifficultyEnum, string}>
     */
    public static function labelProvider(): Iterator
    {
        yield 'easy' => [DifficultyEnum::Easy, 'Easy'];
        yield 'medium' => [DifficultyEnum::Medium, 'Medium'];
        yield 'hard' => [DifficultyEnum::Hard, 'Hard'];
    }

    #[Test]
    public function it_can_be_created_from_value(): void
    {
        $this->assertSame(DifficultyEnum::Easy, DifficultyEnum::from(1));
        $this->assertSame(DifficultyEnum::Medium, DifficultyEnum::from(2));
        $this->assertSame(DifficultyEnum::Hard, DifficultyEnum::from(3));
    }

    #[Test]
    public function it_can_try_from_value(): void
    {
        $this->assertSame(DifficultyEnum::Easy, DifficultyEnum::tryFrom(1));
        $this->assertSame(DifficultyEnum::Medium, DifficultyEnum::tryFrom(2));
        $this->assertSame(DifficultyEnum::Hard, DifficultyEnum::tryFrom(3));
        $this->assertNull(DifficultyEnum::tryFrom(99));
    }
}
