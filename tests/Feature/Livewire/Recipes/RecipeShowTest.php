<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Recipes;

use App\Livewire\Web\Recipes\RecipeShow;
use App\Models\Allergen;
use App\Models\Country;
use App\Models\Cuisine;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Utensil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RecipeShowTest extends TestCase
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
        $recipe = Recipe::factory()->for($this->country)->create([
            'steps_primary' => null,
        ]);

        Livewire::test(RecipeShow::class, ['recipe' => $recipe])
            ->assertStatus(200);
    }

    #[Test]
    public function it_displays_recipe_name(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Delicious Pasta'],
            'steps_primary' => null,
        ]);

        Livewire::test(RecipeShow::class, ['recipe' => $recipe])
            ->assertSee('Delicious Pasta');
    }

    #[Test]
    public function it_loads_all_relationships(): void
    {
        $label = Label::factory()->for($this->country)->create();
        $recipe = Recipe::factory()->for($this->country)->create([
            'label_id' => $label->id,
            'steps_primary' => null,
        ]);

        $tag = Tag::factory()->for($this->country)->create();
        $allergen = Allergen::factory()->for($this->country)->create();
        $cuisine = Cuisine::factory()->for($this->country)->create();
        $utensil = Utensil::factory()->for($this->country)->create();
        $ingredient = Ingredient::factory()->for($this->country)->create();

        $recipe->tags()->attach($tag);
        $recipe->allergens()->attach($allergen);
        $recipe->cuisines()->attach($cuisine);
        $recipe->utensils()->attach($utensil);
        $recipe->ingredients()->attach($ingredient);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $loadedRecipe = $component->get('recipe');
        $this->assertTrue($loadedRecipe->relationLoaded('country'));
        $this->assertTrue($loadedRecipe->relationLoaded('label'));
        $this->assertTrue($loadedRecipe->relationLoaded('tags'));
        $this->assertTrue($loadedRecipe->relationLoaded('allergens'));
        $this->assertTrue($loadedRecipe->relationLoaded('cuisines'));
        $this->assertTrue($loadedRecipe->relationLoaded('utensils'));
        $this->assertTrue($loadedRecipe->relationLoaded('ingredients'));
    }

    #[Test]
    public function it_sets_default_yield(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => []],
                ['yields' => 4, 'ingredients' => []],
            ],
            'steps_primary' => null,
        ]);

        Livewire::test(RecipeShow::class, ['recipe' => $recipe])
            ->assertSet('selectedYield', 2);
    }

    #[Test]
    public function it_returns_available_yields(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => []],
                ['yields' => 4, 'ingredients' => []],
            ],
            'steps_primary' => null,
        ]);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $availableYields = $component->instance()->availableYields;
        $this->assertSame([2, 4], $availableYields);
    }

    #[Test]
    public function it_returns_empty_available_yields_when_no_yields(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => null,
            'steps_primary' => null,
        ]);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $availableYields = $component->instance()->availableYields;
        $this->assertSame([], $availableYields);
    }

    #[Test]
    public function it_returns_ingredients_for_selected_yield(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'hellofresh_ids' => ['ing-123'],
        ]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['id' => 'ing-123', 'amount' => 100, 'unit' => 'g'],
                    ],
                ],
                [
                    'yields' => 4,
                    'ingredients' => [
                        ['id' => 'ing-123', 'amount' => 200, 'unit' => 'g'],
                    ],
                ],
            ],
            'steps_primary' => null,
        ]);
        $recipe->ingredients()->attach($ingredient);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $ingredientsForYield = $component->instance()->ingredientsForYield;
        $this->assertCount(1, $ingredientsForYield);
        $this->assertEquals(100, $ingredientsForYield[0]['amount']);
        $this->assertSame('g', $ingredientsForYield[0]['unit']);
    }

    #[Test]
    public function it_returns_empty_ingredients_when_yield_not_found(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => []],
            ],
            'steps_primary' => null,
        ]);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe])
            ->set('selectedYield', 6);

        $ingredientsForYield = $component->instance()->ingredientsForYield;
        $this->assertSame([], $ingredientsForYield);
    }

    #[Test]
    public function it_returns_steps(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'steps_primary' => [
                ['index' => 1, 'instructions' => 'Step 1', 'images' => []],
                ['index' => 2, 'instructions' => 'Step 2', 'images' => []],
                ['index' => 3, 'instructions' => 'Step 3', 'images' => []],
            ],
        ]);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $steps = $component->instance()->steps;
        $this->assertCount(3, $steps);
    }

    #[Test]
    public function it_returns_empty_steps_when_null(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'steps_primary' => null,
        ]);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $steps = $component->instance()->steps;
        $this->assertSame([], $steps);
    }

    #[Test]
    public function it_returns_nutrition(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'nutrition_primary' => [
                ['name' => 'Calories', 'amount' => 500, 'unit' => 'kcal'],
                ['name' => 'Protein', 'amount' => 25, 'unit' => 'g'],
            ],
            'steps_primary' => null,
        ]);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $nutrition = $component->instance()->nutrition;
        $this->assertCount(2, $nutrition);
    }

    #[Test]
    public function it_returns_empty_nutrition_when_null(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'nutrition_primary' => null,
            'steps_primary' => null,
        ]);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $nutrition = $component->instance()->nutrition;
        $this->assertSame([], $nutrition);
    }

    #[Test]
    public function it_returns_similar_recipes_based_on_shared_tags(): void
    {
        $tag = Tag::factory()->for($this->country)->create();

        $recipe = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Main Recipe'],
            'steps_primary' => null,
        ]);
        $recipe->tags()->attach($tag);

        $similarRecipe = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Similar Recipe'],
            'steps_primary' => null,
        ]);
        $similarRecipe->tags()->attach($tag);

        Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Unrelated Recipe'],
            'steps_primary' => null,
        ]);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $similarRecipes = $component->instance()->similarRecipes;
        $this->assertCount(1, $similarRecipes);
        $this->assertTrue($similarRecipes->first()->is($similarRecipe));
    }

    #[Test]
    public function it_returns_empty_similar_recipes_when_no_tags(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'steps_primary' => null,
        ]);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $similarRecipes = $component->instance()->similarRecipes;
        $this->assertCount(0, $similarRecipes);
    }

    #[Test]
    public function similar_recipes_are_limited_to_4(): void
    {
        $tag = Tag::factory()->for($this->country)->create();

        $recipe = Recipe::factory()->for($this->country)->create([
            'steps_primary' => null,
        ]);
        $recipe->tags()->attach($tag);

        Recipe::factory()->for($this->country)->count(10)->create([
            'steps_primary' => null,
        ])->each(fn (Recipe $similarRecipe) => $similarRecipe->tags()->attach($tag));

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $similarRecipes = $component->instance()->similarRecipes;
        $this->assertCount(4, $similarRecipes);
    }

    #[Test]
    public function similar_recipes_excludes_current_recipe(): void
    {
        $tag = Tag::factory()->for($this->country)->create();

        $recipe = Recipe::factory()->for($this->country)->create([
            'name' => ['en' => 'Main Recipe'],
            'steps_primary' => null,
        ]);
        $recipe->tags()->attach($tag);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $similarRecipes = $component->instance()->similarRecipes;
        $this->assertFalse($similarRecipes->contains($recipe));
    }

    #[Test]
    public function similar_recipes_only_from_same_country(): void
    {
        $tag = Tag::factory()->for($this->country)->create();
        $otherCountry = Country::factory()->create();
        $otherTag = Tag::factory()->for($otherCountry)->create(['name' => $tag->name]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'steps_primary' => null,
        ]);
        $recipe->tags()->attach($tag);

        $otherRecipe = Recipe::factory()->for($otherCountry)->create([
            'steps_primary' => null,
        ]);
        $otherRecipe->tags()->attach($otherTag);

        $component = Livewire::test(RecipeShow::class, ['recipe' => $recipe]);

        $similarRecipes = $component->instance()->similarRecipes;
        $this->assertFalse($similarRecipes->contains($otherRecipe));
    }

    #[Test]
    public function it_can_change_selected_yield(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => []],
                ['yields' => 4, 'ingredients' => []],
            ],
            'steps_primary' => null,
        ]);

        Livewire::test(RecipeShow::class, ['recipe' => $recipe])
            ->assertSet('selectedYield', 2)
            ->set('selectedYield', 4)
            ->assertSet('selectedYield', 4);
    }
}
