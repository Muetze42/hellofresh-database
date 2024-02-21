<?php

namespace Tests\Unit\Http\Requests;

use App\Support\Requests\FilterRequest;
use Tests\TestCase;

/**
 * Class FilterRequestTest.
 *
 * @covers \App\Support\Requests\FilterRequest
 */
final class FilterRequestTest extends TestCase
{
    private FilterRequest $filterRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->filterRequest = new FilterRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->filterRequest);
    }

    public function testAuthorize(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testRules(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
