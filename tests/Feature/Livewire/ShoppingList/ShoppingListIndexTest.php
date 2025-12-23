<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\ShoppingList;

use App\Livewire\ShoppingList\ShoppingListIndex;
use App\Models\Country;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\ShoppingList;
use App\Models\User;
use Livewire\Livewire;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ShoppingListIndexTest extends TestCase
{
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
    }

    #[Test]
    public function it_renders_shopping_list_component(): void
    {
        Livewire::test(ShoppingListIndex::class)
            ->assertOk()
            ->assertViewIs('livewire.shopping-list.shopping-list-index');
    }

    #[Test]
    public function it_loads_recipes_by_ids(): void
    {
        $recipe1 = Recipe::factory()->for($this->country)->create();
        $recipe2 = Recipe::factory()->for($this->country)->create();

        Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe1->id, $recipe2->id])
            ->assertSet('recipeIds', [$recipe1->id, $recipe2->id]);
    }

    #[Test]
    public function it_loads_recipes_with_servings_data(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create();

        Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe->id], [$recipe->id => 4])
            ->assertSet('recipeIds', [$recipe->id])
            ->assertSet('servings', [$recipe->id => 4]);
    }

    #[Test]
    public function it_returns_empty_collection_when_no_recipe_ids(): void
    {
        $component = Livewire::test(ShoppingListIndex::class);

        $recipes = $component->instance()->recipes();
        $this->assertCount(0, $recipes);
    }

    #[Test]
    public function it_fetches_recipes_for_current_country_only(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create();
        $otherCountry = Country::factory()->create(['code' => 'DE', 'locales' => ['de']]);
        $otherRecipe = Recipe::factory()->for($otherCountry)->create();

        $component = Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe->id, $otherRecipe->id]);

        $recipes = $component->instance()->recipes();
        $this->assertCount(1, $recipes);
        $this->assertEquals($recipe->id, $recipes->first()->id);
    }

    #[Test]
    public function it_sorts_recipes_by_input_order(): void
    {
        $recipe1 = Recipe::factory()->for($this->country)->create();
        $recipe2 = Recipe::factory()->for($this->country)->create();
        $recipe3 = Recipe::factory()->for($this->country)->create();

        $component = Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe3->id, $recipe1->id, $recipe2->id]);

        $recipes = $component->instance()->recipes();
        $this->assertEquals($recipe3->id, $recipes->values()[0]->id);
        $this->assertEquals($recipe1->id, $recipes->values()[1]->id);
        $this->assertEquals($recipe2->id, $recipes->values()[2]->id);
    }

    #[Test]
    public function it_gets_yields_for_recipe(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => []],
                ['yields' => 4, 'ingredients' => []],
            ],
        ]);

        $component = Livewire::test(ShoppingListIndex::class);
        $yields = $component->instance()->getYieldsForRecipe($recipe);

        $this->assertEquals([2, 4], $yields);
    }

    #[Test]
    public function it_returns_empty_yields_for_recipe_without_yields(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => null,
        ]);

        $component = Livewire::test(ShoppingListIndex::class);
        $yields = $component->instance()->getYieldsForRecipe($recipe);

        $this->assertEquals([], $yields);
    }

    #[Test]
    public function it_gets_default_yield_from_first_available(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 3, 'ingredients' => []],
                ['yields' => 6, 'ingredients' => []],
            ],
        ]);

        $component = Livewire::test(ShoppingListIndex::class);
        $defaultYield = $component->instance()->getDefaultYield($recipe);

        $this->assertEquals(3, $defaultYield);
    }

    #[Test]
    public function it_returns_2_as_default_yield_when_no_yields(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => null,
        ]);

        $component = Livewire::test(ShoppingListIndex::class);
        $defaultYield = $component->instance()->getDefaultYield($recipe);

        $this->assertEquals(2, $defaultYield);
    }

    #[Test]
    public function it_gets_servings_from_set_value(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [['yields' => 2, 'ingredients' => []]],
        ]);

        $component = Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe->id], [$recipe->id => 4]);

        $servings = $component->instance()->getServingsForRecipe($recipe);

        $this->assertEquals(4, $servings);
    }

    #[Test]
    public function it_gets_default_servings_when_not_set(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [['yields' => 3, 'ingredients' => []]],
        ]);

        $component = Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe->id]);

        $servings = $component->instance()->getServingsForRecipe($recipe);

        $this->assertEquals(3, $servings);
    }

    #[Test]
    public function it_aggregates_ingredients_across_recipes(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'hellofresh_ids' => ['ing-1'],
        ]);

        $recipe1 = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => [['id' => 'ing-1', 'amount' => 100, 'unit' => 'g']]],
            ],
        ]);
        $recipe1->ingredients()->attach($ingredient);

        $recipe2 = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => [['id' => 'ing-1', 'amount' => 50, 'unit' => 'g']]],
            ],
        ]);
        $recipe2->ingredients()->attach($ingredient);

        $component = Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe1->id, $recipe2->id]);

        $aggregated = $component->instance()->aggregatedIngredients();

        $this->assertArrayHasKey($ingredient->id, $aggregated);
        $this->assertEqualsWithDelta(150.0, $aggregated[$ingredient->id]['total'], PHP_FLOAT_EPSILON);
        $this->assertCount(2, $aggregated[$ingredient->id]['items']);
    }

    #[Test]
    public function it_handles_null_amounts_in_aggregation(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'hellofresh_ids' => ['ing-1'],
        ]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => [['id' => 'ing-1', 'amount' => null, 'unit' => '']]],
            ],
        ]);
        $recipe->ingredients()->attach($ingredient);

        $component = Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe->id]);

        $aggregated = $component->instance()->aggregatedIngredients();

        $this->assertEqualsWithDelta(0.0, $aggregated[$ingredient->id]['total'], PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function it_saves_shopping_list_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($this->country)->create();

        $this->actingAs($user);

        Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe->id], [$recipe->id => 4])
            ->set('saveListName', 'My Shopping List')
            ->call('saveList');

        $this->assertDatabaseHas('shopping_lists', [
            'user_id' => $user->id,
            'country_id' => $this->country->id,
            'name' => 'My Shopping List',
        ]);

        $shoppingList = ShoppingList::where('name', 'My Shopping List')->first();
        $this->assertEquals([['recipe_id' => $recipe->id, 'servings' => 4]], $shoppingList->items);
    }

    #[Test]
    public function it_dispatches_require_auth_when_saving_without_login(): void
    {
        Livewire::test(ShoppingListIndex::class)
            ->set('saveListName', 'My List')
            ->call('saveList')
            ->assertDispatched('require-auth');
    }

    #[Test]
    public function it_validates_list_name_when_saving(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ShoppingListIndex::class)
            ->set('saveListName', '')
            ->call('saveList')
            ->assertHasErrors(['saveListName' => 'required']);
    }

    #[Test]
    public function it_validates_list_name_min_length(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ShoppingListIndex::class)
            ->set('saveListName', 'A')
            ->call('saveList')
            ->assertHasErrors(['saveListName' => 'min']);
    }

    #[Test]
    public function it_validates_list_name_max_length(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ShoppingListIndex::class)
            ->set('saveListName', str_repeat('a', 256))
            ->call('saveList')
            ->assertHasErrors(['saveListName' => 'max']);
    }

    #[Test]
    public function it_resets_list_name_after_saving(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ShoppingListIndex::class)
            ->set('saveListName', 'My List')
            ->call('saveList')
            ->assertSet('saveListName', '');
    }

    #[Test]
    public function it_opens_save_modal_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ShoppingListIndex::class)
            ->call('openSaveModal')
            ->assertOk();
    }

    #[Test]
    public function it_dispatches_require_auth_when_opening_modal_without_login(): void
    {
        Livewire::test(ShoppingListIndex::class)
            ->call('openSaveModal')
            ->assertDispatched('require-auth');
    }

    #[Test]
    public function it_detects_print_mode(): void
    {
        $component = Livewire::test(ShoppingListIndex::class)
            ->set('printMode', 'print');

        $this->assertTrue($component->instance()->isPrintMode());
    }

    #[Test]
    public function it_detects_non_print_mode(): void
    {
        $component = Livewire::test(ShoppingListIndex::class)
            ->set('printMode', '');

        $this->assertFalse($component->instance()->isPrintMode());
    }

    #[Test]
    public function it_has_url_bindings_for_mode_and_style(): void
    {
        Livewire::withQueryParams(['mode' => 'print', 'style' => 'per-recipe'])
            ->test(ShoppingListIndex::class)
            ->assertSet('printMode', 'print')
            ->assertSet('printStyle', 'per-recipe');
    }

    #[Test]
    public function it_uses_combined_print_style_by_default(): void
    {
        Livewire::test(ShoppingListIndex::class)
            ->assertSet('printStyle', 'combined');
    }

    #[Test]
    public function it_uses_default_servings_when_not_in_servings_array(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [['yields' => 2, 'ingredients' => []]],
        ]);

        $component = Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe->id], []);

        $this->assertEquals(2, $component->instance()->getServingsForRecipe($recipe));
    }

    #[Test]
    public function it_sorts_aggregated_ingredients_by_name(): void
    {
        $ingredientZ = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Zucchini'],
            'hellofresh_ids' => ['ing-z'],
        ]);
        $ingredientA = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Apple'],
            'hellofresh_ids' => ['ing-a'],
        ]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => [
                    ['id' => 'ing-z', 'amount' => 1, 'unit' => 'pc'],
                    ['id' => 'ing-a', 'amount' => 2, 'unit' => 'pc'],
                ]],
            ],
        ]);
        $recipe->ingredients()->attach([$ingredientZ->id, $ingredientA->id]);

        $component = Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe->id]);

        $aggregated = $component->instance()->aggregatedIngredients();
        $keys = array_keys($aggregated);

        $this->assertEquals($ingredientA->id, $keys[0]);
        $this->assertEquals($ingredientZ->id, $keys[1]);
    }

    #[Test]
    public function it_handles_ingredient_not_found_in_yields(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'hellofresh_ids' => ['ing-1'],
        ]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => [['id' => 'different-id', 'amount' => 100, 'unit' => 'g']]],
            ],
        ]);
        $recipe->ingredients()->attach($ingredient);

        $component = Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe->id]);

        $aggregated = $component->instance()->aggregatedIngredients();

        $this->assertNull($aggregated[$ingredient->id]['items'][0]['amount']);
        $this->assertEquals('', $aggregated[$ingredient->id]['unit']);
    }

    #[Test]
    public function it_uses_servings_for_yields_data_lookup(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'hellofresh_ids' => ['ing-1'],
        ]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => [['id' => 'ing-1', 'amount' => 100, 'unit' => 'g']]],
                ['yields' => 4, 'ingredients' => [['id' => 'ing-1', 'amount' => 200, 'unit' => 'g']]],
            ],
        ]);
        $recipe->ingredients()->attach($ingredient);

        $component = Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe->id], [$recipe->id => 4]);

        $aggregated = $component->instance()->aggregatedIngredients();

        $this->assertEqualsWithDelta(200.0, $aggregated[$ingredient->id]['total'], PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function it_falls_back_to_first_yields_when_servings_not_found(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'hellofresh_ids' => ['ing-1'],
        ]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => [['id' => 'ing-1', 'amount' => 100, 'unit' => 'g']]],
            ],
        ]);
        $recipe->ingredients()->attach($ingredient);

        $component = Livewire::test(ShoppingListIndex::class)
            ->call('loadRecipes', [$recipe->id], [$recipe->id => 99]);

        $aggregated = $component->instance()->aggregatedIngredients();

        $this->assertEqualsWithDelta(100.0, $aggregated[$ingredient->id]['total'], PHP_FLOAT_EPSILON);
    }
}
