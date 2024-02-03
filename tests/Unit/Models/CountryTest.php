<?php

namespace Tests\Unit\Models;

use App\Models\Country;
use Tests\TestCase;

/**
 * Class CountryTest.
 *
 * @covers \App\Models\Country
 */
final class CountryTest extends TestCase
{
    private Country $country;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->country = new Country();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->country);
    }

    public function testSwitch(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
