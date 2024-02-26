<?php

namespace Tests\Feature\Console\Commands\Fix;

use App\Console\Commands\Fix\UpdateMissingRecipeMinutesCommand;
use Tests\TestCase;

/**
 * Class UpdateMissingRecipeMinutesCommandTest.
 *
 * @covers \App\Console\Commands\Fix\UpdateMissingRecipeMinutesCommand
 */
final class UpdateMissingRecipeMinutesCommandTest extends TestCase
{
    private UpdateMissingRecipeMinutesCommand $updateMissingRecipeMinutesCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->updateMissingRecipeMinutesCommand = new UpdateMissingRecipeMinutesCommand();
        $this->app->instance(UpdateMissingRecipeMinutesCommand::class, $this->updateMissingRecipeMinutesCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->updateMissingRecipeMinutesCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:fix:update-missing-recipe-minutes')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
