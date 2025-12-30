<?php

declare(strict_types=1);

namespace Tests\Unit\Livewire\Concerns;

use App\Livewire\Concerns\WithLocalizedContextTrait;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Component;
use Livewire\Livewire;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class WithLocalizedContextTraitTest extends TestCase
{
    use RefreshDatabase;

    private Country $country;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        app()->bind('current.country', fn (): Country => $this->country);
        app()->setLocale('en');
    }

    #[Test]
    public function it_boots_with_country_id_from_current_country(): void
    {
        $component = Livewire::test(TestComponent::class);

        $this->assertSame($this->country->id, $component->get('countryId'));
    }

    #[Test]
    public function it_boots_with_locale_from_app(): void
    {
        app()->setLocale('en');

        $component = Livewire::test(TestComponent::class);

        $this->assertSame('en', $component->get('locale'));
    }

    #[Test]
    public function it_returns_country_from_computed_property(): void
    {
        $component = Livewire::test(TestComponent::class);

        $country = $component->instance()->country();

        $this->assertTrue($country->is($this->country));
    }

    #[Test]
    public function it_restores_context_on_hydrate(): void
    {
        // First request sets up the context
        $component = Livewire::test(TestComponent::class);

        // Clear the binding to simulate a new Livewire request
        app()->forgetInstance('current.country');

        // Simulate subsequent Livewire request by calling hydrate
        $instance = $component->instance();
        $instance->hydrateWithLocalizedContextTrait();

        // Verify the context was restored
        $this->assertTrue(app()->bound('current.country'));
    }

    #[Test]
    public function it_does_not_restore_context_if_already_bound(): void
    {
        $component = Livewire::test(TestComponent::class);

        // Context is already bound
        $instance = $component->instance();

        // This should not throw and should keep existing binding
        $instance->hydrateWithLocalizedContextTrait();

        $this->assertTrue(app()->bound('current.country'));
    }
}

class TestComponent extends Component
{
    use WithLocalizedContextTrait;

    public function render(): string
    {
        return '<div>Test</div>';
    }
}
