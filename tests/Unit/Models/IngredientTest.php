<?php

namespace Tests\Unit\Models;

use App\Models\Ingredient;
use Tests\TestCase;

/**
 * Class IngredientTest.
 *
 * @covers \App\Models\Ingredient
 */
final class IngredientTest extends TestCase
{
    private Ingredient $ingredient;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->ingredient = new Ingredient();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->ingredient);
    }

    public function testRecipes(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testAllergens(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
