<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\GlobalSearch;
use App\Models\Country;
use App\Models\Menu;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class GlobalSearchTest extends TestCase
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
    public function it_can_render(): void
    {
        Livewire::test(GlobalSearch::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_shows_type_to_search_when_empty(): void
    {
        Livewire::test(GlobalSearch::class)
            ->assertSee(__('Type to search...'));
    }

    #[Test]
    public function it_shows_no_results_found_when_search_has_no_matches(): void
    {
        Livewire::test(GlobalSearch::class)
            ->set('search', 'nonexistentrecipename12345')
            ->assertSee(__('No results found.'));
    }

    #[Test]
    public function it_finds_recipes_by_name(): void
    {
        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Delicious Pasta Carbonara'],
        ]);

        Livewire::test(GlobalSearch::class)
            ->set('search', 'Pasta')
            ->assertSee('Delicious Pasta Carbonara');
    }

    #[Test]
    public function it_finds_recipes_by_headline(): void
    {
        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Simple Dish'],
            'headline' => ['en' => 'With creamy mushroom sauce'],
        ]);

        Livewire::test(GlobalSearch::class)
            ->set('search', 'mushroom')
            ->assertSee('Simple Dish');
    }

    #[Test]
    public function it_only_finds_recipes_for_current_country(): void
    {
        $otherCountry = Country::factory()->create(['code' => 'DE']);

        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'American Recipe'],
        ]);
        Recipe::factory()->for($otherCountry)->create([
            'name' => ['en' => 'German Recipe'],
        ]);

        Livewire::test(GlobalSearch::class)
            ->set('search', 'Recipe')
            ->assertSee('American Recipe')
            ->assertDontSee('German Recipe');
    }

    #[Test]
    public function it_shows_menus_for_recipes(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Weekly Special'],
        ]);

        $menu = Menu::factory()->for($this->country)->create([
            'year_week' => 202501,
        ]);
        $menu->recipes()->attach($recipe);

        Livewire::test(GlobalSearch::class)
            ->set('search', 'Weekly')
            ->assertSee('Weekly Special')
            ->assertSee(__('Week') . ' 01');
    }

    #[Test]
    public function it_limits_results_to_ten_recipes(): void
    {
        Recipe::factory()->for($this->country)->count(15)->create([
            'name' => ['en' => 'Test Recipe'],
        ]);

        $component = Livewire::test(GlobalSearch::class)
            ->set('search', 'Test');

        $this->assertCount(10, $component->instance()->recipes);
    }

    #[Test]
    public function it_can_navigate_to_recipe(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Navigable Recipe'],
            'hellofresh_id' => 'abc123',
        ]);

        Livewire::test(GlobalSearch::class)
            ->call('selectRecipe', $recipe->id)
            ->assertRedirect();
    }
}
