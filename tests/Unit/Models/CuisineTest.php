<?php

namespace Tests\Unit\Models;

use App\Models\Cuisine;
use Tests\TestCase;

/**
 * Class CuisineTest.
 *
 * @covers \App\Models\Cuisine
 */
final class CuisineTest extends TestCase
{
    private Cuisine $cuisine;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->cuisine = new Cuisine();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->cuisine);
    }

    public function testRecipes(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
