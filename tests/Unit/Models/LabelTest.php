<?php

namespace Tests\Unit\Models;

use App\Models\Label;
use Tests\TestCase;

/**
 * Class LabelTest.
 *
 * @covers \App\Models\Label
 */
final class LabelTest extends TestCase
{
    private Label $label;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->label = new Label();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->label);
    }

    public function testRecipes(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
