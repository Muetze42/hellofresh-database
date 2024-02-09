<?php

namespace Tests\Unit\Http\Resources\PageIndices;

use App\Http\Resources\Indices\LabelIndexResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class LabelResourceTest.
 *
 * @covers \App\Http\Resources\Indices\LabelIndexResource
 */
final class LabelResourceTest extends TestCase
{
    private LabelIndexResource $labelResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->labelResource = new LabelIndexResource();
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
