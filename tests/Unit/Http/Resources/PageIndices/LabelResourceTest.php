<?php

namespace Tests\Unit\Http\Resources\PageIndices;

use App\Http\Resources\PageIndices\LabelResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class LabelResourceTest.
 *
 * @covers \App\Http\Resources\PageIndices\LabelResource
 */
final class LabelResourceTest extends TestCase
{
    private LabelResource $labelResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->labelResource = new LabelResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->labelResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->labelResource->toArray($request));
    }
}
