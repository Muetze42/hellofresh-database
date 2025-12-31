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
        int $expected
    ): void {
        $request = Request::create('/', 'GET', $requestValue !== null ? ['perPage' => $requestValue] : []);

        $this->assertSame($expected, validated_per_page($request));
    }

    /**
     * Config values: default=50, max=200, min=10
     *
     * @return Iterator<string, array{requestValue: int|null, expected: int}>
     */
    public static function validatedPerPageDataProvider(): Iterator
    {
        yield 'no request value uses default' => [
            'requestValue' => null,
            'expected' => 50,
        ];
        yield 'value within range returns value' => [
            'requestValue' => 100,
            'expected' => 100,
        ];
        yield 'value at minimum returns value' => [
            'requestValue' => 10,
            'expected' => 10,
        ];
        yield 'value at maximum returns value' => [
            'requestValue' => 200,
            'expected' => 200,
        ];
        yield 'value below minimum returns minimum' => [
            'requestValue' => 5,
            'expected' => 10,
        ];
        yield 'value above maximum returns maximum' => [
            'requestValue' => 500,
            'expected' => 200,
        ];
        yield 'zero value returns minimum' => [
            'requestValue' => 0,
            'expected' => 10,
        ];
        yield 'negative value returns minimum' => [
            'requestValue' => -50,
            'expected' => 10,
        ];
    }

    public function test_validated_per_page_uses_container_request_when_null(): void
    {
        $this->get('/?perPage=75');

        $this->assertSame(75, validated_per_page());
    }

    public function test_validated_per_page_uses_default_from_config(): void
    {
        $request = Request::create('/', 'GET');

        $result = validated_per_page($request);

        $this->assertSame(config('api.pagination.per_page_default'), $result);
    }

    public function test_validated_per_page_enforces_config_bounds(): void
    {
        $min = config('api.pagination.per_page_min');
        $max = config('api.pagination.per_page_max');

        $requestBelowMin = Request::create('/', 'GET', ['perPage' => $min - 5]);
        $requestAboveMax = Request::create('/', 'GET', ['perPage' => $max + 1000]);

        $this->assertSame($min, validated_per_page($requestBelowMin));
        $this->assertSame($max, validated_per_page($requestAboveMax));
    }
}
