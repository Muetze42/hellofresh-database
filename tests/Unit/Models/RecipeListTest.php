<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\Recipe;
use App\Models\RecipeList;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RecipeListTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_user_relationship(): void
    {
        $recipeList = RecipeList::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $recipeList->user());
        $this->assertInstanceOf(User::class, $recipeList->user);
    }

    #[Test]
    public function it_has_country_relationship(): void
    {
        $recipeList = RecipeList::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $recipeList->country());
        $this->assertInstanceOf(Country::class, $recipeList->country);
    }

    #[Test]
    public function it_has_recipes_relationship(): void
    {
        $recipeList = RecipeList::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $recipeList->recipes());
    }

    #[Test]
    public function it_can_have_many_recipes(): void
    {
        $country = Country::factory()->create();
        $recipeList = RecipeList::factory()->forCountry($country)->create();
        $recipes = Recipe::factory()->count(3)->for($country)->create();

        foreach ($recipes as $recipe) {
            $recipeList->recipes()->attach($recipe, ['added_at' => now()]);
        }

        $this->assertCount(3, $recipeList->recipes);
    }

    #[Test]
    public function recipes_are_ordered_by_added_at_descending(): void
    {
        $country = Country::factory()->create();
        $recipeList = RecipeList::factory()->forCountry($country)->create();
        $recipes = Recipe::factory()->count(3)->for($country)->create();

        $recipeList->recipes()->attach($recipes[0], ['added_at' => now()->subDays(2)]);
        $recipeList->recipes()->attach($recipes[1], ['added_at' => now()->subDay()]);
        $recipeList->recipes()->attach($recipes[2], ['added_at' => now()]);

        $orderedRecipes = $recipeList->recipes;

        $this->assertTrue($orderedRecipes->first()->is($recipes[2]));
        $this->assertTrue($orderedRecipes->last()->is($recipes[0]));
    }

    #[Test]
    public function recipes_pivot_includes_added_at(): void
    {
        $country = Country::factory()->create();
        $recipeList = RecipeList::factory()->forCountry($country)->create();
        $recipe = Recipe::factory()->for($country)->create();

        $recipeList->recipes()->attach($recipe, ['added_at' => now()]);

        $attachedRecipe = $recipeList->recipes->first();
        $this->assertNotNull($attachedRecipe->pivot->added_at);
    }

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $recipeList = new RecipeList();
        $fillable = $recipeList->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('description', $fillable);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        RecipeList::factory()->create([
            'name' => 'My Recipe List',
            'description' => 'A collection of my favorite recipes',
        ]);

        $this->assertDatabaseHas('recipe_lists', [
            'name' => 'My Recipe List',
            'description' => 'A collection of my favorite recipes',
        ]);
    }

    #[Test]
    public function it_can_be_created_for_specific_user(): void
    {
        $user = User::factory()->create();
        $recipeList = RecipeList::factory()->forUser($user)->create();

        $this->assertTrue($recipeList->user->is($user));
    }

    #[Test]
    public function it_can_be_created_for_specific_country(): void
    {
        $country = Country::factory()->create();
        $recipeList = RecipeList::factory()->forCountry($country)->create();

        $this->assertTrue($recipeList->country->is($country));
    }

    #[Test]
    public function it_can_be_created_without_description(): void
    {
        $recipeList = RecipeList::factory()->withoutDescription()->create();

        $this->assertNull($recipeList->description);
    }
}
