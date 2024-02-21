<?php

namespace Tests\Feature\Console\Commands\Development;

use App\Console\Commands\Development\FlagsCommand;
use Tests\TestCase;

/**
 * Class FlagsCommmandTest.
 *
 * @covers \App\Console\Commands\Development\FlagsCommand
 */
final class FlagsCommmandTest extends TestCase
{
    private FlagsCommand $flagsCommmand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->flagsCommmand = new FlagsCommand();
        $this->app->instance(FlagsCommand::class, $this->flagsCommmand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->flagsCommmand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:development:flags-commmand')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
