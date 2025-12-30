<?php

declare(strict_types=1);

namespace Tests\Unit;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class NearestAllowedTest extends TestCase
{
    /**
     * @param  int[]  $allowed
     */
    #[DataProvider('nearestAllowedDataProvider')]
    public function test_nearest_allowed_returns_correct_value(int $value, array $allowed, int $expected): void
    {
        $this->assertSame($expected, nearest_allowed($value, $allowed));
    }

    /**
     * @return Iterator<string, array{value: int, allowed: int[], expected: int}>
     */
    public static function nearestAllowedDataProvider(): Iterator
    {
        yield 'value exists in allowed list' => [
            'value' => 50,
            'allowed' => [10, 25, 50, 100],
            'expected' => 50,
        ];
        yield 'value below minimum returns minimum' => [
            'value' => 5,
            'allowed' => [10, 25, 50, 100],
            'expected' => 10,
        ];
        yield 'value above maximum returns maximum' => [
            'value' => 200,
            'allowed' => [10, 25, 50, 100],
            'expected' => 100,
        ];
        yield 'value between allowed returns nearest lower' => [
            'value' => 30,
            'allowed' => [10, 25, 50, 100],
            'expected' => 25,
        ];
        yield 'value just below next tier returns current tier' => [
            'value' => 49,
            'allowed' => [10, 25, 50, 100],
            'expected' => 25,
        ];
        yield 'value just above tier returns that tier' => [
            'value' => 51,
            'allowed' => [10, 25, 50, 100],
            'expected' => 50,
        ];
        yield 'unsorted allowed list gets sorted' => [
            'value' => 30,
            'allowed' => [100, 10, 50, 25],
            'expected' => 25,
        ];
        yield 'single allowed value below' => [
            'value' => 5,
            'allowed' => [10],
            'expected' => 10,
        ];
        yield 'single allowed value above' => [
            'value' => 15,
            'allowed' => [10],
            'expected' => 10,
        ];
        yield 'single allowed value exact' => [
            'value' => 10,
            'allowed' => [10],
            'expected' => 10,
        ];
        yield 'two values returns min when below' => [
            'value' => 5,
            'allowed' => [10, 100],
            'expected' => 10,
        ];
        yield 'two values returns max when above' => [
            'value' => 150,
            'allowed' => [10, 100],
            'expected' => 100,
        ];
        yield 'two values returns lower when between' => [
            'value' => 50,
            'allowed' => [10, 100],
            'expected' => 10,
        ];
        yield 'negative value below minimum' => [
            'value' => -5,
            'allowed' => [10, 25, 50],
            'expected' => 10,
        ];
        yield 'zero value below minimum' => [
            'value' => 0,
            'allowed' => [10, 25, 50],
            'expected' => 10,
        ];
    }
}
