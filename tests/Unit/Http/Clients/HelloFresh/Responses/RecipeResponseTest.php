<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Clients\HelloFresh\Responses;

use App\Http\Clients\HelloFresh\Responses\RecipeResponse;
use GuzzleHttp\Psr7\Response as Psr7Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RecipeResponseTest extends TestCase
{
    #[Test]
    public function it_returns_array_data(): void
    {
        $response = $this->createResponse([
            'id' => 'recipe-123',
            'name' => 'Test Recipe',
            'slug' => 'test-recipe',
        ]);

        $data = $response->array();

        $this->assertIsArray($data);
        $this->assertSame('recipe-123', $data['id']);
    }

    #[Test]
    public function it_returns_id(): void
    {
        $response = $this->createResponse([
            'id' => 'abc-123-xyz',
            'name' => 'Recipe',
            'slug' => 'recipe',
        ]);

        $this->assertSame('abc-123-xyz', $response->id());
    }

    #[Test]
    public function it_returns_name(): void
    {
        $response = $this->createResponse([
            'id' => 'recipe-id',
            'name' => 'Delicious Pasta Carbonara',
            'slug' => 'delicious-pasta-carbonara',
        ]);

        $this->assertSame('Delicious Pasta Carbonara', $response->name());
    }

    #[Test]
    public function it_returns_slug(): void
    {
        $response = $this->createResponse([
            'id' => 'recipe-id',
            'name' => 'Chicken Tikka Masala',
            'slug' => 'chicken-tikka-masala',
        ]);

        $this->assertSame('chicken-tikka-masala', $response->slug());
    }

    protected function createResponse(array $data): RecipeResponse
    {
        $psr7Response = new Psr7Response(200, [], json_encode($data));

        return new RecipeResponse($psr7Response);
    }
}
