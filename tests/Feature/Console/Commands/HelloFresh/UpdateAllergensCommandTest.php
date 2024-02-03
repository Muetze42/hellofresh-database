<?php

namespace Tests\Feature\Console\Commands\HelloFresh;

use App\Console\Commands\HelloFresh\UpdateAllergensCommand;
use Tests\TestCase;

/**
 * Class UpdateAllergensCommandTest.
 *
 * @covers \App\Console\Commands\HelloFresh\UpdateAllergensCommand
 */
final class UpdateAllergensCommandTest extends TestCase
{
    private UpdateAllergensCommand $updateAllergensCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->updateAllergensCommand = new UpdateAllergensCommand();
        $this->app->instance(UpdateAllergensCommand::class, $this->updateAllergensCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->updateAllergensCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:hello-fresh:update-allergens')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
