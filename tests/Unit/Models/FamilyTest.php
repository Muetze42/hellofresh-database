<?php

namespace Tests\Unit\Models;

use App\Models\Family;
use Tests\TestCase;

/**
 * Class FamilyTest.
 *
 * @covers \App\Models\Family
 */
final class FamilyTest extends TestCase
{
    private Family $family;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->family = new Family();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->family);
    }

    public function testRecipes(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
