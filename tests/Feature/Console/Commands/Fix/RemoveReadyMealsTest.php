<?php

namespace Tests\Feature\Console\Commands\Fix;

use App\Console\Commands\Fix\RemoveReadyMealsCommand;
use Tests\TestCase;

/**
 * Class RemoveReadyMealsTest.
 *
 * @covers \App\Console\Commands\Fix\RemoveReadyMealsCommand
 */
final class RemoveReadyMealsTest extends TestCase
{
    private RemoveReadyMealsCommand $removeReadyMeals;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->removeReadyMeals = new RemoveReadyMealsCommand();
        $this->app->instance(RemoveReadyMealsCommand::class, $this->removeReadyMeals);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->removeReadyMeals);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:fix:remove-ready-meals')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
