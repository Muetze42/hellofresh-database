<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Clients\HelloFresh\Responses;

use App\Http\Clients\HelloFresh\Responses\RecipesResponse;
use GuzzleHttp\Psr7\Response as Psr7Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use TypeError;

final class RecipesResponseTest extends TestCase
{
    #[Test]
    public function it_returns_array_data(): void
    {
        $response = $this->createResponse([
            'items' => [],
            'take' => 50,
            'skip' => 0,
            'count' => 0,
            'total' => 100,
        ]);

        $data = $response->array();

        $this->assertIsArray($data);
        $this->assertSame(100, $data['total']);
    }

    #[Test]
    public function it_returns_items(): void
    {
        $response = $this->createResponse([
            'items' => [
                ['id' => 'recipe-1', 'name' => 'Recipe 1'],
                ['id' => 'recipe-2', 'name' => 'Recipe 2'],
            ],
            'take' => 50,
            'skip' => 0,
            'count' => 2,
            'total' => 2,
        ]);

        $items = $response->items();

        $this->assertCount(2, $items);
        $this->assertSame('recipe-1', $items[0]['id']);
        $this->assertSame('Recipe 2', $items[1]['name']);
    }

    #[Test]
    public function it_returns_total(): void
    {
        $response = $this->createResponse([
            'items' => [],
            'take' => 50,
            'skip' => 0,
            'count' => 0,
            'total' => 1500,
        ]);

        $this->assertSame(1500, $response->total());
    }

    #[Test]
    public function it_returns_take(): void
    {
        $response = $this->createResponse([
            'items' => [],
            'take' => 100,
            'skip' => 0,
            'count' => 0,
            'total' => 500,
        ]);

        $this->assertSame(100, $response->take());
    }

    #[Test]
    public function it_returns_skip(): void
    {
        $response = $this->createResponse([
            'items' => [],
            'take' => 50,
            'skip' => 200,
            'count' => 0,
            'total' => 500,
        ]);

        $this->assertSame(200, $response->skip());
    }

    #[Test]
    public function has_more_pages_returns_true_when_more_available(): void
    {
        $response = $this->createResponse([
            'items' => [],
            'take' => 50,
            'skip' => 0,
            'count' => 50,
            'total' => 100,
        ]);

        $this->assertTrue($response->hasMorePages());
    }

    #[Test]
    public function has_more_pages_returns_false_when_at_end(): void
    {
        $response = $this->createResponse([
            'items' => [],
            'take' => 50,
            'skip' => 50,
            'count' => 50,
            'total' => 100,
        ]);

        $this->assertFalse($response->hasMorePages());
    }

    #[Test]
    public function has_more_pages_returns_false_when_past_end(): void
    {
        $response = $this->createResponse([
            'items' => [],
            'take' => 50,
            'skip' => 100,
            'count' => 0,
            'total' => 100,
        ]);

        $this->assertFalse($response->hasMorePages());
    }

    #[Test]
    public function it_calculates_next_skip(): void
    {
        $response = $this->createResponse([
            'items' => [],
            'take' => 50,
            'skip' => 100,
            'count' => 50,
            'total' => 500,
        ]);

        $this->assertSame(150, $response->nextSkip());
    }

    #[Test]
    public function it_throws_type_error_for_non_array_json(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The JSON decoded body is not an array');

        $psr7Response = new Psr7Response(200, [], '"not an array"');
        $response = new RecipesResponse($psr7Response);
        $response->array();
    }

    protected function createResponse(array $data): RecipesResponse
    {
        $psr7Response = new Psr7Response(200, [], json_encode($data));

        return new RecipesResponse($psr7Response);
    }
}
