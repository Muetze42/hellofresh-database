<?php

namespace Tests\Feature\Console\Commands\HelloFresh;

use App\Console\Commands\HelloFresh\UpdateRecipeCommand;
use Tests\TestCase;

/**
 * Class UpdateRecipeCommandTest.
 *
 * @covers \App\Console\Commands\HelloFresh\UpdateRecipeCommand
 */
final class UpdateRecipeCommandTest extends TestCase
{
    private UpdateRecipeCommand $updateRecipeCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->updateRecipeCommand = new UpdateRecipeCommand();
        $this->app->instance(UpdateRecipeCommand::class, $this->updateRecipeCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->updateRecipeCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:hello-fresh:update-recipe')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
