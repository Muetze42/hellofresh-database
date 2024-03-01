<?php

namespace Tests\Feature\Console\Commands\Fix;

use App\Console\Commands\Fix\DeleteRecipesWithoutImageCommand;
use Tests\TestCase;

/**
 * Class DeleteRecipesWithoutImageCommandTest.
 *
 * @covers \App\Console\Commands\Fix\DeleteRecipesWithoutImageCommand
 */
final class DeleteRecipesWithoutImageCommandTest extends TestCase
{
    private DeleteRecipesWithoutImageCommand $deleteRecipesWithoutImageCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->deleteRecipesWithoutImageCommand = new DeleteRecipesWithoutImageCommand();
        $this->app->instance(DeleteRecipesWithoutImageCommand::class, $this->deleteRecipesWithoutImageCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->deleteRecipesWithoutImageCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:fix:delete-recipes-without-image')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
