<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Http\Request;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class ValidatedPerPageTest extends TestCase
{
    #[DataProvider('validatedPerPageDataProvider')]
    public function test_validated_per_page_returns_correct_value(
        ?int $requestValue,
        int $default,
        int $max,
        int $min,
        int $expected
    ): void {
        $request = Request::create('/', 'GET', $requestValue !== null ? ['perPage' => $requestValue] : []);

        $this->assertSame($expected, validated_per_page($request, $default, $max, $min));
    }

    /**
     * @return Iterator<string, array{requestValue: int|null, default: int, max: int, min: int, expected: int}>
     */
    public static function validatedPerPageDataProvider(): Iterator
    {
        yield 'no request value uses default' => [
            'requestValue' => null,
            'default' => 100,
            'max' => 1000,
            'min' => 10,
            'expected' => 100,
        ];
        yield 'value within range returns value' => [
            'requestValue' => 50,
            'default' => 100,
            'max' => 1000,
            'min' => 10,
            'expected' => 50,
        ];
        yield 'value at minimum returns value' => [
            'requestValue' => 10,
            'default' => 100,
            'max' => 1000,
            'min' => 10,
            'expected' => 10,
        ];
        yield 'value at maximum returns value' => [
            'requestValue' => 1000,
            'default' => 100,
            'max' => 1000,
            'min' => 10,
            'expected' => 1000,
        ];
        yield 'value below minimum returns minimum' => [
            'requestValue' => 5,
            'default' => 100,
            'max' => 1000,
            'min' => 10,
            'expected' => 10,
        ];
        yield 'value above maximum returns maximum' => [
            'requestValue' => 2000,
            'default' => 100,
            'max' => 1000,
            'min' => 10,
            'expected' => 1000,
        ];
        yield 'zero value returns minimum' => [
            'requestValue' => 0,
            'default' => 100,
            'max' => 1000,
            'min' => 10,
            'expected' => 10,
        ];
        yield 'negative value returns minimum' => [
            'requestValue' => -50,
            'default' => 100,
            'max' => 1000,
            'min' => 10,
            'expected' => 10,
        ];
        yield 'custom min max range works' => [
            'requestValue' => 25,
            'default' => 50,
            'max' => 100,
            'min' => 20,
            'expected' => 25,
        ];
        yield 'value between min and custom range' => [
            'requestValue' => 15,
            'default' => 50,
            'max' => 100,
            'min' => 20,
            'expected' => 20,
        ];
    }

    public function test_validated_per_page_uses_container_request_when_null(): void
    {
        $this->get('/?perPage=75');

        $this->assertSame(75, validated_per_page());
    }

    public function test_validated_per_page_uses_default_parameters(): void
    {
        $request = Request::create('/', 'GET', ['perPage' => 500]);

        $result = validated_per_page($request);

        $this->assertSame(500, $result);
    }

    public function test_validated_per_page_default_parameters_enforce_bounds(): void
    {
        $requestBelowMin = Request::create('/', 'GET', ['perPage' => 5]);
        $requestAboveMax = Request::create('/', 'GET', ['perPage' => 5000]);

        $this->assertSame(10, validated_per_page($requestBelowMin));
        $this->assertSame(1000, validated_per_page($requestAboveMax));
    }
}
