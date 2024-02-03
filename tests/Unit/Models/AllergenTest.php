<?php

namespace Tests\Unit\Models;

use App\Models\Allergen;
use Tests\TestCase;

/**
 * Class AllergenTest.
 *
 * @covers \App\Models\Allergen
 */
final class AllergenTest extends TestCase
{
    private Allergen $allergen;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->allergen = new Allergen();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->allergen);
    }

    public function testRecipes(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testIngredients(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
