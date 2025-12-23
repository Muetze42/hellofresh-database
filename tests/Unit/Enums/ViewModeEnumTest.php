<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\ViewModeEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ViewModeEnumTest extends TestCase
{
    #[Test]
    public function it_has_correct_cases(): void
    {
        $cases = ViewModeEnum::cases();

        $this->assertCount(2, $cases);
        $this->assertContains(ViewModeEnum::Grid, $cases);
        $this->assertContains(ViewModeEnum::List, $cases);
    }

    #[Test]
    public function it_has_correct_values(): void
    {
        $this->assertSame('grid', ViewModeEnum::Grid->value);
        $this->assertSame('list', ViewModeEnum::List->value);
    }

    #[Test]
    public function it_can_be_created_from_value(): void
    {
        $this->assertSame(ViewModeEnum::Grid, ViewModeEnum::from('grid'));
        $this->assertSame(ViewModeEnum::List, ViewModeEnum::from('list'));
    }

    #[Test]
    public function it_can_try_from_value(): void
    {
        $this->assertSame(ViewModeEnum::Grid, ViewModeEnum::tryFrom('grid'));
        $this->assertSame(ViewModeEnum::List, ViewModeEnum::tryFrom('list'));
        $this->assertNull(ViewModeEnum::tryFrom('invalid'));
    }
}
