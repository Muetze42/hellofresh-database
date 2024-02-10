<?php

namespace Tests\Feature\Console\Commands\Countries;

use App\Console\Commands\Countries\DeactivateCommand;
use Tests\TestCase;

/**
 * Class DeactivateCommandTest.
 *
 * @covers \App\Console\Commands\Countries\DeactivateCommand
 */
final class DeactivateCommandTest extends TestCase
{
    private DeactivateCommand $deactivateCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->deactivateCommand = new DeactivateCommand();
        $this->app->instance(DeactivateCommand::class, $this->deactivateCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->deactivateCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:countries:deactivate')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
