<?php

namespace Tests\Unit\Models;

use App\Models\Utensil;
use Tests\TestCase;

/**
 * Class UtensilTest.
 *
 * @covers \App\Models\Utensil
 */
final class UtensilTest extends TestCase
{
    private Utensil $utensil;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->utensil = new Utensil();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->utensil);
    }

    public function testRecipes(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
