<?php

namespace Tests\Feature\Console\Commands\HelloFresh;

use App\Console\Commands\HelloFresh\UpdateCuisinesCommand;
use Tests\TestCase;

/**
 * Class UpdateCuisinesCommandTest.
 *
 * @covers \App\Console\Commands\HelloFresh\UpdateCuisinesCommand
 */
final class UpdateCuisinesCommandTest extends TestCase
{
    private UpdateCuisinesCommand $updateCuisinesCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->updateCuisinesCommand = new UpdateCuisinesCommand();
        $this->app->instance(UpdateCuisinesCommand::class, $this->updateCuisinesCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->updateCuisinesCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:hello-fresh:update-cuisines')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
