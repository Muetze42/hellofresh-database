<?php

namespace Tests\Unit\Models;

use App\Models\Tag;
use Tests\TestCase;

/**
 * Class TagTest.
 *
 * @covers \App\Models\Tag
 */
final class TagTest extends TestCase
{
    private Tag $tag;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->tag = new Tag();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->tag);
    }

    public function testRecipes(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
