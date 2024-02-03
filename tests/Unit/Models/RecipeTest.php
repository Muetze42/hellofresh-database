<?php

namespace Tests\Unit\Models;

use App\Models\Recipe;
use Tests\TestCase;

/**
 * Class RecipeTest.
 *
 * @covers \App\Models\Recipe
 */
final class RecipeTest extends TestCase
{
    private Recipe $recipe;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->recipe = new Recipe();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->recipe);
    }

    public function testAllergens(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testCuisines(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testIngredients(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testTags(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testUtensils(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testLabel(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testCategory(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testFamily(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
