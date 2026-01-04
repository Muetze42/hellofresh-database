<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\BringExportController;
use App\Models\Country;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Tests\TestCase;

/**
 * Unit tests for BringExportController.
 *
 * These tests focus on the business logic methods of the controller
 * by testing them directly rather than through HTTP routing.
 */
final class BringExportControllerTest extends TestCase
{
    protected Country $country;

    protected BringExportController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        app()->bind('current.country', fn (): Country => $this->country);
        app()->setLocale('en');

        $this->controller = new BringExportController();
    }

    /**
     * Call a protected/private method on the controller.
     */
    protected function callMethod(string $method, array $args = []): mixed
    {
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod($method);

        return $method->invokeArgs($this->controller, $args);
    }

    #[Test]
    public function it_parses_empty_recipe_ids_string(): void
    {
        $result = $this->callMethod('parseRecipeIds', ['']);

        $this->assertSame([], $result);
    }

    #[Test]
    public function it_parses_single_recipe_id(): void
    {
        $result = $this->callMethod('parseRecipeIds', ['123']);

        $this->assertSame([123], $result);
    }

    #[Test]
    public function it_parses_multiple_recipe_ids(): void
    {
        $result = $this->callMethod('parseRecipeIds', ['1,2,3']);

        $this->assertSame([1, 2, 3], $result);
    }

    #[Test]
    public function it_parses_empty_servings_string(): void
    {
        $result = $this->callMethod('parseServings', ['']);

        $this->assertSame([], $result);
    }

    #[Test]
    public function it_parses_single_serving(): void
    {
        $result = $this->callMethod('parseServings', ['1:4']);

        $this->assertSame([1 => 4], $result);
    }

    #[Test]
    public function it_parses_multiple_servings(): void
    {
        $result = $this->callMethod('parseServings', ['1:2,3:4']);

        $this->assertSame([1 => 2, 3 => 4], $result);
    }

    #[Test]
    public function it_ignores_invalid_serving_format(): void
    {
        $result = $this->callMethod('parseServings', ['invalid,1:2,alsoinvalid']);

        $this->assertSame([1 => 2], $result);
    }

    #[Test]
    public function it_returns_empty_yields_data_for_non_matching_servings(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Salt', 'amount' => 5, 'unit' => 'g'],
                    ],
                ],
            ],
        ]);

        $result = $this->callMethod('getYieldsDataForServings', [$recipe, 99]);

        $this->assertSame([], $result);
    }

    #[Test]
    public function it_returns_yields_data_for_matching_servings(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Salt', 'amount' => 5, 'unit' => 'g'],
                    ],
                ],
            ],
        ]);

        $result = $this->callMethod('getYieldsDataForServings', [$recipe, 2]);

        $this->assertArrayHasKey('Salt', $result);
        $this->assertSame(5.0, $result['Salt']['amount']);
        $this->assertSame('g', $result['Salt']['unit']);
    }

    #[Test]
    public function it_uses_secondary_yields_when_primary_is_null(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => null,
            'yields_secondary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Milk', 'amount' => 250, 'unit' => 'ml'],
                    ],
                ],
            ],
        ]);

        $result = $this->callMethod('getYieldsDataForServings', [$recipe, 2]);

        $this->assertArrayHasKey('Milk', $result);
        $this->assertSame(250.0, $result['Milk']['amount']);
    }

    #[Test]
    public function it_returns_empty_when_both_yields_are_null(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => null,
            'yields_secondary' => null,
        ]);

        $result = $this->callMethod('getYieldsDataForServings', [$recipe, 2]);

        $this->assertSame([], $result);
    }

    #[Test]
    public function it_returns_empty_when_yields_is_not_array(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => 'not an array',
            'yields_secondary' => null,
        ]);

        $result = $this->callMethod('getYieldsDataForServings', [$recipe, 2]);

        $this->assertSame([], $result);
    }

    #[Test]
    public function it_handles_ingredient_without_amount(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Salt'],
                    ],
                ],
            ],
        ]);

        $result = $this->callMethod('getYieldsDataForServings', [$recipe, 2]);

        $this->assertNull($result['Salt']['amount']);
        $this->assertSame('', $result['Salt']['unit']);
    }

    #[Test]
    public function it_finds_ingredient_in_yields(): void
    {
        $yieldsData = [
            'Salt' => ['amount' => 5.0, 'unit' => 'g'],
            'Pepper' => ['amount' => 2.0, 'unit' => 'g'],
        ];

        $result = $this->callMethod('findIngredientInYields', [$yieldsData, 'Salt']);

        $this->assertSame(5.0, $result['amount']);
        $this->assertSame('g', $result['unit']);
    }

    #[Test]
    public function it_returns_default_when_ingredient_not_in_yields(): void
    {
        $yieldsData = [
            'Salt' => ['amount' => 5.0, 'unit' => 'g'],
        ];

        $result = $this->callMethod('findIngredientInYields', [$yieldsData, 'NotFound']);

        $this->assertNull($result['amount']);
        $this->assertSame('', $result['unit']);
    }

    #[Test]
    public function it_aggregates_ingredients_from_single_recipe(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Flour'],
        ]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Flour', 'amount' => 100, 'unit' => 'g'],
                    ],
                ],
            ],
        ]);
        $recipe->ingredients()->attach($ingredient);

        $recipes = collect([$recipe->load('ingredients')]);
        $result = $this->callMethod('aggregateIngredients', [$recipes, []]);

        $this->assertCount(1, $result);
        $this->assertSame('Flour', $result[0]['name']);
        $this->assertSame('100 g', $result[0]['amount']);
    }

    #[Test]
    public function it_aggregates_same_ingredient_from_multiple_recipes(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Flour'],
        ]);

        $recipe1 = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Flour', 'amount' => 100, 'unit' => 'g'],
                    ],
                ],
            ],
        ]);
        $recipe1->ingredients()->attach($ingredient);

        $recipe2 = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Flour', 'amount' => 200, 'unit' => 'g'],
                    ],
                ],
            ],
        ]);
        $recipe2->ingredients()->attach($ingredient);

        $recipes = collect([$recipe1->load('ingredients'), $recipe2->load('ingredients')]);
        $result = $this->callMethod('aggregateIngredients', [$recipes, []]);

        $this->assertCount(1, $result);
        $this->assertSame('Flour', $result[0]['name']);
        $this->assertSame('300 g', $result[0]['amount']);
    }

    #[Test]
    public function it_handles_different_units_for_same_ingredient(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Sugar'],
        ]);

        $recipe1 = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Sugar', 'amount' => 50, 'unit' => 'g'],
                    ],
                ],
            ],
        ]);
        $recipe1->ingredients()->attach($ingredient);

        $recipe2 = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Sugar', 'amount' => 2, 'unit' => 'tbsp'],
                    ],
                ],
            ],
        ]);
        $recipe2->ingredients()->attach($ingredient);

        $recipes = collect([$recipe1->load('ingredients'), $recipe2->load('ingredients')]);
        $result = $this->callMethod('aggregateIngredients', [$recipes, []]);

        $this->assertCount(1, $result);
        $this->assertSame('Sugar', $result[0]['name']);
        $this->assertStringContainsString('50 g', $result[0]['amount']);
        $this->assertStringContainsString('2 tbsp', $result[0]['amount']);
    }

    #[Test]
    public function it_handles_ingredient_without_amount_in_aggregation(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Salt'],
        ]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Salt', 'amount' => null, 'unit' => ''],
                    ],
                ],
            ],
        ]);
        $recipe->ingredients()->attach($ingredient);

        $recipes = collect([$recipe->load('ingredients')]);
        $result = $this->callMethod('aggregateIngredients', [$recipes, []]);

        $this->assertCount(1, $result);
        $this->assertSame('Salt', $result[0]['name']);
        $this->assertSame('', $result[0]['amount']);
    }

    #[Test]
    public function it_uses_custom_servings_from_map(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Butter'],
        ]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Butter', 'amount' => 50, 'unit' => 'g'],
                    ],
                ],
                [
                    'yields' => 4,
                    'ingredients' => [
                        ['name' => 'Butter', 'amount' => 100, 'unit' => 'g'],
                    ],
                ],
            ],
        ]);
        $recipe->ingredients()->attach($ingredient);

        $recipes = collect([$recipe->load('ingredients')]);
        $servingsMap = [$recipe->id => 4];
        $result = $this->callMethod('aggregateIngredients', [$recipes, $servingsMap]);

        $this->assertCount(1, $result);
        $this->assertSame('100 g', $result[0]['amount']);
    }

    #[Test]
    public function it_defaults_to_servings_of_2_when_not_in_map(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Butter'],
        ]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Butter', 'amount' => 50, 'unit' => 'g'],
                    ],
                ],
                [
                    'yields' => 4,
                    'ingredients' => [
                        ['name' => 'Butter', 'amount' => 100, 'unit' => 'g'],
                    ],
                ],
            ],
        ]);
        $recipe->ingredients()->attach($ingredient);

        $recipes = collect([$recipe->load('ingredients')]);
        $result = $this->callMethod('aggregateIngredients', [$recipes, []]);

        $this->assertCount(1, $result);
        $this->assertSame('50 g', $result[0]['amount']);
    }

    #[Test]
    public function it_formats_decimal_amounts_correctly(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Oil'],
        ]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Oil', 'amount' => 2.5, 'unit' => 'tbsp'],
                    ],
                ],
            ],
        ]);
        $recipe->ingredients()->attach($ingredient);

        $recipes = collect([$recipe->load('ingredients')]);
        $result = $this->callMethod('aggregateIngredients', [$recipes, []]);

        $this->assertSame('2.5 tbsp', $result[0]['amount']);
    }

    #[Test]
    public function it_formats_whole_numbers_without_decimals(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create([
            'name' => ['en' => 'Eggs'],
        ]);

        $recipe = Recipe::factory()->for($this->country)->create([
            'yields_primary' => [
                [
                    'yields' => 2,
                    'ingredients' => [
                        ['name' => 'Eggs', 'amount' => 3.0, 'unit' => 'pcs'],
                    ],
                ],
            ],
        ]);
        $recipe->ingredients()->attach($ingredient);

        $recipes = collect([$recipe->load('ingredients')]);
        $result = $this->callMethod('aggregateIngredients', [$recipes, []]);

        $this->assertSame('3 pcs', $result[0]['amount']);
    }

    #[Test]
    public function it_invokes_controller_and_returns_view(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create();
        $request = Request::create('/shopping-list/bring', 'GET', [
            'recipes' => (string) $recipe->id,
        ]);

        $view = $this->controller->__invoke($request, $this->country);

        $this->assertSame('web::bring-export', $view->name());
        $this->assertArrayHasKey('ingredients', $view->getData());
        $this->assertArrayHasKey('recipes', $view->getData());
    }

    #[Test]
    public function it_filters_recipes_by_country(): void
    {
        $otherCountry = Country::factory()->create(['code' => 'DE']);
        $recipeInCountry = Recipe::factory()->for($this->country)->create();
        $recipeOtherCountry = Recipe::factory()->for($otherCountry)->create();

        $request = Request::create('/shopping-list/bring', 'GET', [
            'recipes' => $recipeInCountry->id . ',' . $recipeOtherCountry->id,
        ]);

        $view = $this->controller->__invoke($request, $this->country);

        $recipes = $view->getData()['recipes'];
        $this->assertCount(1, $recipes);
        $this->assertSame($recipeInCountry->id, $recipes->first()->id);
    }

    #[Test]
    public function it_returns_empty_recipes_for_empty_request(): void
    {
        $request = Request::create('/shopping-list/bring', 'GET');

        $view = $this->controller->__invoke($request, $this->country);

        $this->assertCount(0, $view->getData()['recipes']);
        $this->assertSame([], $view->getData()['ingredients']);
    }

    #[Test]
    public function it_loads_ingredients_relationship(): void
    {
        $ingredient = Ingredient::factory()->for($this->country)->create();
        $recipe = Recipe::factory()->for($this->country)->create();
        $recipe->ingredients()->attach($ingredient);

        $request = Request::create('/shopping-list/bring', 'GET', [
            'recipes' => (string) $recipe->id,
        ]);

        $view = $this->controller->__invoke($request, $this->country);

        $recipes = $view->getData()['recipes'];
        $this->assertTrue($recipes->first()->relationLoaded('ingredients'));
    }
}
