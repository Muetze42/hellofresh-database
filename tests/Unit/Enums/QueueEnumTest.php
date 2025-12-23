<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\QueueEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class QueueEnumTest extends TestCase
{
    #[Test]
    public function it_has_correct_cases(): void
    {
        $cases = QueueEnum::cases();

        $this->assertCount(4, $cases);
        $this->assertContains(QueueEnum::Default, $cases);
        $this->assertContains(QueueEnum::HelloFresh, $cases);
        $this->assertContains(QueueEnum::Import, $cases);
        $this->assertContains(QueueEnum::Long, $cases);
    }

    #[Test]
    public function it_has_correct_values(): void
    {
        $this->assertSame('default', QueueEnum::Default->value);
        $this->assertSame('hellofresh', QueueEnum::HelloFresh->value);
        $this->assertSame('import', QueueEnum::Import->value);
        $this->assertSame('long', QueueEnum::Long->value);
    }

    #[Test]
    public function it_can_be_created_from_value(): void
    {
        $this->assertSame(QueueEnum::Default, QueueEnum::from('default'));
        $this->assertSame(QueueEnum::HelloFresh, QueueEnum::from('hellofresh'));
        $this->assertSame(QueueEnum::Import, QueueEnum::from('import'));
        $this->assertSame(QueueEnum::Long, QueueEnum::from('long'));
    }

    #[Test]
    public function it_can_try_from_value(): void
    {
        $this->assertSame(QueueEnum::Default, QueueEnum::tryFrom('default'));
        $this->assertSame(QueueEnum::HelloFresh, QueueEnum::tryFrom('hellofresh'));
        $this->assertNull(QueueEnum::tryFrom('invalid'));
    }
}
