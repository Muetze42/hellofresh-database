<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\RecipeResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class RecipeResourceTest.
 *
 * @covers \App\Http\Resources\RecipeResource
 */
final class RecipeResourceTest extends TestCase
{
    private RecipeResource $recipeResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->recipeResource = new RecipeResource();
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
