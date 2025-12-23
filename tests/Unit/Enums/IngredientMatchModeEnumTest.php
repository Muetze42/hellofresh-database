<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\IngredientMatchModeEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IngredientMatchModeEnumTest extends TestCase
{
    #[Test]
    public function it_has_correct_cases(): void
    {
        $cases = IngredientMatchModeEnum::cases();

        $this->assertCount(2, $cases);
        $this->assertContains(IngredientMatchModeEnum::Any, $cases);
        $this->assertContains(IngredientMatchModeEnum::All, $cases);
    }

    #[Test]
    public function it_has_correct_values(): void
    {
        $this->assertSame('any', IngredientMatchModeEnum::Any->value);
        $this->assertSame('all', IngredientMatchModeEnum::All->value);
    }

    #[Test]
    public function it_can_be_created_from_value(): void
    {
        $this->assertSame(IngredientMatchModeEnum::Any, IngredientMatchModeEnum::from('any'));
        $this->assertSame(IngredientMatchModeEnum::All, IngredientMatchModeEnum::from('all'));
    }

    #[Test]
    public function it_can_try_from_value(): void
    {
        $this->assertSame(IngredientMatchModeEnum::Any, IngredientMatchModeEnum::tryFrom('any'));
        $this->assertSame(IngredientMatchModeEnum::All, IngredientMatchModeEnum::tryFrom('all'));
        $this->assertNull(IngredientMatchModeEnum::tryFrom('invalid'));
    }
}
