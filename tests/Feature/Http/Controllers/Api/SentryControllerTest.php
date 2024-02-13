<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\SentryTunnelController;
use Tests\TestCase;

/**
 * Class SentryControllerTest.
 *
 * @covers \App\Http\Controllers\Api\SentryTunnelController
 */
final class SentryControllerTest extends TestCase
{
    private SentryTunnelController $sentryController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->sentryController = new SentryTunnelController();
        $this->app->instance(SentryTunnelController::class, $this->sentryController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->sentryController);
    }

    public function test__invoke(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
