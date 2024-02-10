<?php

namespace Tests\Feature\Console\Commands\Countries;

use App\Console\Commands\Countries\ActivateCommand;
use Tests\TestCase;

/**
 * Class ActivateTest.
 *
 * @covers \App\Console\Commands\Countries\ActivateCommand
 */
final class ActivateTest extends TestCase
{
    private ActivateCommand $activate;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->activate = new ActivateCommand();
        $this->app->instance(ActivateCommand::class, $this->activate);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->activate);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:countries:activate')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
