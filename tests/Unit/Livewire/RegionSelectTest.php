<?php

declare(strict_types=1);

namespace Tests\Unit\Livewire;

use App\Livewire\RegionSelect;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RegionSelectTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_render(): void
    {
        Livewire::test(RegionSelect::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_converts_country_code_to_flag_emoji(): void
    {
        $component = Livewire::test(RegionSelect::class);

        $usFlag = $component->instance()->getFlagEmoji('US');
        $deFlag = $component->instance()->getFlagEmoji('DE');
        $frFlag = $component->instance()->getFlagEmoji('FR');

        $this->assertNotEmpty($usFlag);
        $this->assertNotEmpty($deFlag);
        $this->assertNotEmpty($frFlag);
        $this->assertNotSame($usFlag, $deFlag);
    }

    #[Test]
    public function it_handles_lowercase_country_code(): void
    {
        $component = Livewire::test(RegionSelect::class);

        $upperFlag = $component->instance()->getFlagEmoji('US');
        $lowerFlag = $component->instance()->getFlagEmoji('us');

        $this->assertSame($upperFlag, $lowerFlag);
    }

    #[Test]
    public function it_returns_only_active_countries(): void
    {
        Country::factory()->create(['code' => 'US', 'active' => true, 'prep_min' => 5, 'recipes_count' => 100, 'ingredients_count' => 50]);
        Country::factory()->create(['code' => 'DE', 'active' => true, 'prep_min' => 5, 'recipes_count' => 100, 'ingredients_count' => 50]);
        Country::factory()->inactive()->create(['code' => 'FR']);

        $component = Livewire::test(RegionSelect::class);
        $countries = $component->instance()->countries();

        $this->assertCount(2, $countries);
        $this->assertTrue($countries->contains('code', 'US'));
        $this->assertTrue($countries->contains('code', 'DE'));
        $this->assertFalse($countries->contains('code', 'FR'));
    }

    #[Test]
    public function it_orders_countries_by_code(): void
    {
        Country::factory()->create(['code' => 'US', 'active' => true, 'prep_min' => 5, 'recipes_count' => 100, 'ingredients_count' => 50]);
        Country::factory()->create(['code' => 'AT', 'active' => true, 'prep_min' => 5, 'recipes_count' => 100, 'ingredients_count' => 50]);
        Country::factory()->create(['code' => 'DE', 'active' => true, 'prep_min' => 5, 'recipes_count' => 100, 'ingredients_count' => 50]);

        $component = Livewire::test(RegionSelect::class);
        $countries = $component->instance()->countries();

        $this->assertSame('AT', $countries->first()->code);
        $this->assertSame('US', $countries->last()->code);
    }

    #[Test]
    public function it_returns_empty_collection_when_no_active_countries(): void
    {
        Country::factory()->inactive()->create(['code' => 'US']);

        $component = Livewire::test(RegionSelect::class);
        $countries = $component->instance()->countries();

        $this->assertCount(0, $countries);
    }
}
