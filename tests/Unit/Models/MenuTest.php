<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\Menu;
use App\Models\Recipe;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MenuTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_country_relationship(): void
    {
        $menu = Menu::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $menu->country());
        $this->assertInstanceOf(Country::class, $menu->country);
    }

    #[Test]
    public function it_has_recipes_relationship(): void
    {
        $menu = Menu::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $menu->recipes());
    }

    #[Test]
    public function it_can_have_many_recipes(): void
    {
        $country = Country::factory()->create();
        $menu = Menu::factory()->for($country)->create();
        $recipes = Recipe::factory()->count(5)->for($country)->create();

        $menu->recipes()->attach($recipes);

        $this->assertCount(5, $menu->recipes);
    }

    #[Test]
    public function it_casts_year_week_to_integer(): void
    {
        $menu = Menu::factory()->create(['year_week' => 202501]);

        $this->assertIsInt($menu->year_week);
        $this->assertSame(202501, $menu->year_week);
    }

    #[Test]
    public function it_casts_start_to_date(): void
    {
        $menu = Menu::factory()->create(['start' => '2025-01-01']);

        $this->assertInstanceOf(Carbon::class, $menu->start);
    }

    #[Test]
    public function it_hides_year_week_on_serialization(): void
    {
        $menu = Menu::factory()->create();
        $serialized = $menu->toArray();

        $this->assertArrayNotHasKey('year_week', $serialized);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        $menu = Menu::factory()->create();

        $this->assertNotNull($menu->year_week);
        $this->assertNotNull($menu->start);
        $this->assertNotNull($menu->country_id);
    }

    #[Test]
    public function year_week_is_correctly_calculated(): void
    {
        $menu = Menu::factory()->create([
            'year_week' => 202503,
            'start' => Date::create(2025, 1, 13),
        ]);

        $this->assertSame(202503, $menu->year_week);
    }
}
