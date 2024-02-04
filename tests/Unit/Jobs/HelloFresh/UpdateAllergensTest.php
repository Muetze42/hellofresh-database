<?php

namespace Tests\Unit\Jobs\HelloFresh;

use App\Jobs\HelloFresh\UpdateAllergensJob;
use Tests\TestCase;

/**
 * Class UpdateAllergensTest.
 *
 * @covers \App\Jobs\HelloFresh\UpdateAllergensJob
 */
final class UpdateAllergensTest extends TestCase
{
    private UpdateAllergensJob $updateAllergens;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->updateAllergens = new UpdateAllergensJob();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->updateAllergens);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->updateAllergens->handle();
    }
}
