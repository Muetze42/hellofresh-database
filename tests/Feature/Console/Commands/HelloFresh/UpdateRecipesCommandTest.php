<?php

namespace Tests\Feature\Console\Commands\HelloFresh;

use App\Console\Commands\HelloFresh\UpdateRecipesCommand;
use Tests\TestCase;

/**
 * Class UpdateRecipesCommandTest.
 *
 * @covers \App\Console\Commands\HelloFresh\UpdateRecipesCommand
 */
final class UpdateRecipesCommandTest extends TestCase
{
    private UpdateRecipesCommand $updateRecipesCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->updateRecipesCommand = new UpdateRecipesCommand();
        $this->app->instance(UpdateRecipesCommand::class, $this->updateRecipesCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->updateRecipesCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:hello-fresh:update-recipes')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
