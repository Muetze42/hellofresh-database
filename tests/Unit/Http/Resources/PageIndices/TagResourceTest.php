<?php

namespace Tests\Unit\Http\Resources\PageIndices;

use App\Http\Resources\Indices\TagIndexResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class TagResourceTest.
 *
 * @covers \App\Http\Resources\Indices\TagIndexResource
 */
final class TagResourceTest extends TestCase
{
    private TagIndexResource $tagResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->tagResource = new TagIndexResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->tagResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->tagResource->toArray($request));
    }
}
