<?php

namespace Tests\Feature\Console\Commands\Assets;

use App\Console\Commands\Assets\GenerateSocialPreview;
use Tests\TestCase;

/**
 * Class GenerateSocialPreviewTest.
 *
 * @covers \App\Console\Commands\Assets\GenerateSocialPreview
 */
final class GenerateSocialPreviewTest extends TestCase
{
    private GenerateSocialPreview $generateSocialPreview;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->generateSocialPreview = new GenerateSocialPreview();
        $this->app->instance(GenerateSocialPreview::class, $this->generateSocialPreview);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->generateSocialPreview);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:assets:generate-social-preview')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
