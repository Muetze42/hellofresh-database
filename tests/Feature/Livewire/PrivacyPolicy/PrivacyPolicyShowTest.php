<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\PrivacyPolicy;

use App\Livewire\Web\Legal\PrivacyPolicyShow;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PrivacyPolicyShowTest extends TestCase
{
    use RefreshDatabase;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        app()->bind('current.country', fn (): Country => $country);
        app()->setLocale('en');
    }

    #[Test]
    public function it_can_render(): void
    {
        Livewire::test(PrivacyPolicyShow::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_returns_null_when_no_policy_file_exists(): void
    {
        File::shouldReceive('exists')
            ->andReturn(false);

        $component = Livewire::test(PrivacyPolicyShow::class);

        $this->assertNull($component->instance()->content());
    }

    #[Test]
    public function it_uses_locale_specific_file_when_available(): void
    {
        $localePath = resource_path('docs/privacy/en.md');
        $content = '# Privacy Policy';

        File::shouldReceive('exists')
            ->with($localePath)
            ->andReturn(true);

        File::shouldReceive('get')
            ->with($localePath)
            ->andReturn($content);

        $component = Livewire::test(PrivacyPolicyShow::class);

        $result = $component->instance()->content();

        $this->assertNotNull($result);
        $this->assertStringContainsString('Privacy Policy', (string) $result);
    }

    #[Test]
    public function it_falls_back_to_english_when_locale_file_missing(): void
    {
        app()->setLocale('de');

        $dePath = resource_path('docs/privacy/de.md');
        $enPath = resource_path('docs/privacy/en.md');
        $content = '# English Privacy Policy';

        File::shouldReceive('exists')
            ->with($dePath)
            ->andReturn(false);

        File::shouldReceive('exists')
            ->with($enPath)
            ->andReturn(true);

        File::shouldReceive('get')
            ->with($enPath)
            ->andReturn($content);

        $component = Livewire::test(PrivacyPolicyShow::class);

        $result = $component->instance()->content();

        $this->assertNotNull($result);
        $this->assertStringContainsString('English Privacy Policy', (string) $result);
    }
}
