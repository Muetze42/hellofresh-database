<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Tests\TestCase;

/**
 * Class CategoryTest.
 *
 * @covers \App\Models\Category
 */
final class CategoryTest extends TestCase
{
    private Category $category;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->category = new Category();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->category);
    }

    public function testRecipes(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
