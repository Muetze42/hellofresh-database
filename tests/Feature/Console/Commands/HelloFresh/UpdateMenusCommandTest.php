<?php

namespace Tests\Feature\Console\Commands\HelloFresh;

use App\Console\Commands\HelloFresh\UpdateMenusCommand;
use Tests\TestCase;

/**
 * Class UpdateMenusCommandTest.
 *
 * @covers \App\Console\Commands\HelloFresh\UpdateMenusCommand
 */
final class UpdateMenusCommandTest extends TestCase
{
    private UpdateMenusCommand $updateMenusCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->updateMenusCommand = new UpdateMenusCommand();
        $this->app->instance(UpdateMenusCommand::class, $this->updateMenusCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->updateMenusCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:hello-fresh:update-menus')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
