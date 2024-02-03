<?php

namespace Tests\Feature\Console\Commands\HelloFresh;

use App\Console\Commands\HelloFresh\UpdateIngredientsCommand;
use Tests\TestCase;

/**
 * Class UpdateIngredientsCommandTest.
 *
 * @covers \App\Console\Commands\HelloFresh\UpdateIngredientsCommand
 */
final class UpdateIngredientsCommandTest extends TestCase
{
    private UpdateIngredientsCommand $updateIngredientsCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->updateIngredientsCommand = new UpdateIngredientsCommand();
        $this->app->instance(UpdateIngredientsCommand::class, $this->updateIngredientsCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->updateIngredientsCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:hello-fresh:update-ingredients')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
