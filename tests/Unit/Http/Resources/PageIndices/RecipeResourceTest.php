<?php

namespace Tests\Unit\Http\Resources\PageIndices;

use App\Http\Resources\Indices\RecipeIndexResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class RecipeResourceTest.
 *
 * @covers \App\Http\Resources\Indices\RecipeIndexResource
 */
final class RecipeResourceTest extends TestCase
{
    private RecipeIndexResource $recipeResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->recipeResource = new RecipeIndexResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->recipeResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->recipeResource->toArray($request));
    }
}
