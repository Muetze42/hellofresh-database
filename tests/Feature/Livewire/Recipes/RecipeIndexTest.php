<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Recipes;

use App\Enums\IngredientMatchModeEnum;
use App\Enums\RecipeSortEnum;
use App\Enums\ViewModeEnum;
use App\Livewire\Web\Recipes\RecipeIndex;
use App\Models\Allergen;
use App\Models\Country;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RecipeIndexTest extends TestCase
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
            'prep_min' => 5,
            'prep_max' => 60,
            'total_min' => 15,
            'total_max' => 120,
        ]);

        app()->bind('current.country', fn (): Country => $this->country);
        app()->setLocale('en');
    }

    #[Test]
    public function it_can_render(): void
    {
        Livewire::test(RecipeIndex::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_displays_recipes_for_current_country(): void
    {
        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Test Recipe'],
        ]);

        Livewire::test(RecipeIndex::class)
            ->assertSee('Test Recipe');
    }

    #[Test]
    public function it_does_not_display_recipes_from_other_countries(): void
    {
        $otherCountry = Country::factory()->create(['code' => 'DE']);
        Recipe::factory()->for($otherCountry)->create([
            'name' => ['en' => 'German Recipe'],
        ]);

        Livewire::test(RecipeIndex::class)
            ->assertDontSee('German Recipe');
    }

    #[Test]
    public function it_can_filter_recipes_with_pdf(): void
    {
        Recipe::factory()->for($this->country)->withPdf()->create([
            'name' => ['en' => 'Recipe With PDF'],
        ]);
        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Recipe Without PDF'],
        ]);

        Livewire::test(RecipeIndex::class)
            ->set('filterHasPdf', true)
            ->assertSee('Recipe With PDF')
            ->assertDontSee('Recipe Without PDF');
    }

    #[Test]
    public function it_can_exclude_recipes_by_allergen(): void
    {
        $allergen = Allergen::factory()->for($this->country)->create([
            'name' => ['en' => 'Gluten'],
        ]);

        $recipeWithAllergen = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Recipe With Gluten'],
        ]);
        $recipeWithAllergen->allergens()->attach($allergen);

        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Gluten Free Recipe'],
        ]);

        Livewire::test(RecipeIndex::class)
            ->set('excludedAllergenIds', [$allergen->id])
            ->assertSee('Gluten Free Recipe')
            ->assertDontSee('Recipe With Gluten');
    }

    #[Test]
    public function it_can_filter_recipes_by_tag(): void
    {
        $tag = Tag::factory()->for($this->country)->create([
            'name' => ['en' => 'Vegetarian'],
        ]);

        $recipeWithTag = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Vegetarian Recipe'],
        ]);
        $recipeWithTag->tags()->attach($tag);

        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Non-Vegetarian Recipe'],
        ]);

        Livewire::test(RecipeIndex::class)
            ->set('tagIds', [$tag->id])
            ->assertSee('Vegetarian Recipe')
            ->assertDontSee('Non-Vegetarian Recipe');
    }

    #[Test]
    public function it_can_exclude_recipes_by_tag(): void
    {
        $tag = Tag::factory()->for($this->country)->create([
            'name' => ['en' => 'Spicy'],
        ]);

        $spicyRecipe = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Spicy Recipe'],
        ]);
        $spicyRecipe->tags()->attach($tag);

        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Mild Recipe'],
        ]);

        Livewire::test(RecipeIndex::class)
            ->set('excludedTagIds', [$tag->id])
            ->assertSee('Mild Recipe')
            ->assertDontSee('Spicy Recipe');
    }

    #[Test]
    public function it_can_filter_recipes_by_label(): void
    {
        $label = Label::factory()->for($this->country)->create([
            'name' => ['en' => 'Premium'],
        ]);

        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Premium Recipe'],
            'label_id' => $label->id,
        ]);

        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Regular Recipe'],
        ]);

        Livewire::test(RecipeIndex::class)
            ->set('labelIds', [$label->id])
            ->assertSee('Premium Recipe')
            ->assertDontSee('Regular Recipe');
    }

    #[Test]
    public function it_can_filter_recipes_by_difficulty(): void
    {
        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Easy Recipe'],
            'difficulty' => 1,
        ]);

        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Hard Recipe'],
            'difficulty' => 3,
        ]);

        Livewire::test(RecipeIndex::class)
            ->set('difficultyLevels', [1])
            ->assertSee('Easy Recipe')
            ->assertDontSee('Hard Recipe');
    }

    #[Test]
    public function it_can_filter_recipes_by_ingredient_any_mode(): void
    {
        $ingredient1 = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Tomato'],
        ]);
        $ingredient2 = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Cheese'],
        ]);

        $recipeWithTomato = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Tomato Recipe'],
        ]);
        $recipeWithTomato->ingredients()->attach($ingredient1);

        $recipeWithCheese = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Cheese Recipe'],
        ]);
        $recipeWithCheese->ingredients()->attach($ingredient2);

        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'No Ingredient Recipe'],
        ]);

        Livewire::test(RecipeIndex::class)
            ->set('ingredientIds', [$ingredient1->id, $ingredient2->id])
            ->set('ingredientMatchMode', IngredientMatchModeEnum::Any->value)
            ->assertSee('Tomato Recipe')
            ->assertSee('Cheese Recipe')
            ->assertDontSee('No Ingredient Recipe');
    }

    #[Test]
    public function it_can_filter_recipes_by_ingredient_all_mode(): void
    {
        $ingredient1 = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Tomato'],
        ]);
        $ingredient2 = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Cheese'],
        ]);

        $recipeWithBoth = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Pizza Recipe'],
        ]);
        $recipeWithBoth->ingredients()->attach([$ingredient1->id, $ingredient2->id]);

        $recipeWithTomato = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Tomato Only Recipe'],
        ]);
        $recipeWithTomato->ingredients()->attach($ingredient1);

        Livewire::test(RecipeIndex::class)
            ->set('ingredientIds', [$ingredient1->id, $ingredient2->id])
            ->set('ingredientMatchMode', IngredientMatchModeEnum::All->value)
            ->assertSee('Pizza Recipe')
            ->assertDontSee('Tomato Only Recipe');
    }

    #[Test]
    public function it_can_exclude_recipes_by_ingredient(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Onion'],
        ]);

        $recipeWithOnion = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Recipe With Onion'],
        ]);
        $recipeWithOnion->ingredients()->attach($ingredient);

        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Recipe Without Onion'],
        ]);

        // First verify both recipes show without filter
        Livewire::test(RecipeIndex::class)
            ->assertSee('Recipe With Onion')
            ->assertSee('Recipe Without Onion')
            // Then apply the filter
            ->set('excludedIngredientIds', [$ingredient->id])
            ->assertSee('Recipe Without Onion')
            ->assertDontSee('Recipe With Onion');
    }

    #[Test]
    public function it_can_clear_all_filters(): void
    {
        $tag = Tag::factory()->for($this->country)->create();

        Livewire::test(RecipeIndex::class)
            ->set('filterHasPdf', true)
            ->set('tagIds', [$tag->id])
            ->set('difficultyLevels', [1, 2])
            ->call('clearFilters')
            ->assertSet('filterHasPdf', false)
            ->assertSet('tagIds', [])
            ->assertSet('difficultyLevels', []);
    }

    #[Test]
    public function it_calculates_active_filter_count(): void
    {
        $tag = Tag::factory()->for($this->country)->create();
        $allergen = Allergen::factory()->for($this->country)->create();

        Livewire::test(RecipeIndex::class)
            ->assertSet('activeFilterCount', 0)
            ->set('filterHasPdf', true)
            ->assertSet('activeFilterCount', 1)
            ->set('tagIds', [$tag->id])
            ->assertSet('activeFilterCount', 2)
            ->set('excludedAllergenIds', [$allergen->id])
            ->assertSet('activeFilterCount', 3);
    }

    #[Test]
    public function it_persists_view_mode_to_session(): void
    {
        Livewire::test(RecipeIndex::class)
            ->set('viewMode', ViewModeEnum::List->value);

        $this->assertSame(ViewModeEnum::List->value, session('view_mode'));
    }

    #[Test]
    public function it_persists_sort_to_session(): void
    {
        Livewire::test(RecipeIndex::class)
            ->set('sortBy', RecipeSortEnum::OldestFirst->value);

        $key = sprintf('recipe_filter_%d_sort', $this->country->id);
        $this->assertSame(RecipeSortEnum::OldestFirst->value, session($key));
    }

    #[Test]
    public function it_can_filter_by_menu(): void
    {
        $menu = Menu::factory()->for($this->country)->create([
            'year_week' => 202501,
            'start' => Date::today(),
        ]);

        $menuRecipe = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Menu Recipe'],
        ]);
        $menu->recipes()->attach($menuRecipe);

        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Other Recipe'],
        ]);

        Livewire::test(RecipeIndex::class, ['menu' => $menu])
            ->assertSee('Menu Recipe')
            ->assertDontSee('Other Recipe');
    }

    #[Test]
    public function it_loads_allergens_for_current_country(): void
    {
        Allergen::factory()->for($this->country)->create([
            'name' => ['en' => 'Gluten'],
            'active' => true,
        ]);

        $otherCountry = Country::factory()->create(['code' => 'DE']);
        Allergen::factory()->for($otherCountry)->create([
            'name' => ['en' => 'Other Allergen'],
        ]);

        $component = Livewire::test(RecipeIndex::class);

        $allergens = $component->instance()->allergens;
        $this->assertCount(1, $allergens);
        $this->assertSame($this->country->id, $allergens->first()->country_id);
    }

    #[Test]
    public function it_loads_only_active_tags(): void
    {
        Tag::factory()->for($this->country)->create([
            'name' => ['en' => 'Active Tag'],
            'active' => true,
        ]);
        Tag::factory()->for($this->country)->inactive()->create([
            'name' => ['en' => 'Inactive Tag'],
        ]);

        $component = Livewire::test(RecipeIndex::class);

        $tags = $component->instance()->tags;
        $this->assertCount(1, $tags);
    }

    #[Test]
    public function it_loads_only_active_labels(): void
    {
        Label::factory()->for($this->country)->create([
            'name' => ['en' => 'Active Label'],
            'active' => true,
        ]);
        Label::factory()->for($this->country)->inactive()->create([
            'name' => ['en' => 'Inactive Label'],
        ]);

        $component = Livewire::test(RecipeIndex::class);

        $labels = $component->instance()->labels;
        $this->assertCount(1, $labels);
    }

    #[Test]
    public function toggle_tag_adds_tag_to_filter(): void
    {
        $tag = Tag::factory()->for($this->country)->create();

        Livewire::test(RecipeIndex::class)
            ->assertSet('tagIds', [])
            ->call('toggleTag', $tag->id)
            ->assertSet('tagIds', [$tag->id]);

        $key = sprintf('recipe_filter_%d_tags', $this->country->id);
        $this->assertSame([$tag->id], session($key));
    }

    #[Test]
    public function toggle_tag_removes_existing_tag_from_filter(): void
    {
        $tag = Tag::factory()->for($this->country)->create();

        Livewire::test(RecipeIndex::class)
            ->set('tagIds', [$tag->id])
            ->call('toggleTag', $tag->id)
            ->assertSet('tagIds', []);
    }

    #[Test]
    public function is_tag_active_returns_true_for_active_tag(): void
    {
        $tag = Tag::factory()->for($this->country)->create();

        $component = Livewire::test(RecipeIndex::class)
            ->set('tagIds', [$tag->id]);

        $this->assertTrue($component->instance()->isTagActive($tag->id));
    }

    #[Test]
    public function is_tag_active_returns_false_for_inactive_tag(): void
    {
        $tag = Tag::factory()->for($this->country)->create();

        $component = Livewire::test(RecipeIndex::class)
            ->set('tagIds', []);

        $this->assertFalse($component->instance()->isTagActive($tag->id));
    }
}
