<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Allergen;
use App\Models\Country;
use App\Models\Cuisine;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\RecipeList;
use App\Models\ShoppingList;
use App\Models\Tag;
use App\Models\Utensil;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CountryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_recipes_relationship(): void
    {
        $country = Country::factory()->create();

        $this->assertInstanceOf(HasMany::class, $country->recipes());
    }

    #[Test]
    public function it_can_have_many_recipes(): void
    {
        $country = Country::factory()->create();
        Recipe::factory()->count(3)->for($country)->create();

        $this->assertCount(3, $country->recipes);
    }

    #[Test]
    public function it_has_allergens_relationship(): void
    {
        $country = Country::factory()->create();

        $this->assertInstanceOf(HasMany::class, $country->allergens());
    }

    #[Test]
    public function it_can_have_many_allergens(): void
    {
        $country = Country::factory()->create();
        Allergen::factory()->count(3)->for($country)->create();

        $this->assertCount(3, $country->allergens);
    }

    #[Test]
    public function it_has_cuisines_relationship(): void
    {
        $country = Country::factory()->create();

        $this->assertInstanceOf(HasMany::class, $country->cuisines());
    }

    #[Test]
    public function it_can_have_many_cuisines(): void
    {
        $country = Country::factory()->create();
        Cuisine::factory()->count(3)->for($country)->create();

        $this->assertCount(3, $country->cuisines);
    }

    #[Test]
    public function it_has_ingredients_relationship(): void
    {
        $country = Country::factory()->create();

        $this->assertInstanceOf(HasMany::class, $country->ingredients());
    }

    #[Test]
    public function it_can_have_many_ingredients(): void
    {
        $country = Country::factory()->create();
        Ingredient::factory()->count(3)->for($country)->create();

        $this->assertCount(3, $country->ingredients);
    }

    #[Test]
    public function it_has_labels_relationship(): void
    {
        $country = Country::factory()->create();

        $this->assertInstanceOf(HasMany::class, $country->labels());
    }

    #[Test]
    public function it_can_have_many_labels(): void
    {
        $country = Country::factory()->create();
        Label::factory()->count(3)->for($country)->create();

        $this->assertCount(3, $country->labels);
    }

    #[Test]
    public function it_has_tags_relationship(): void
    {
        $country = Country::factory()->create();

        $this->assertInstanceOf(HasMany::class, $country->tags());
    }

    #[Test]
    public function it_can_have_many_tags(): void
    {
        $country = Country::factory()->create();
        Tag::factory()->count(3)->for($country)->create();

        $this->assertCount(3, $country->tags);
    }

    #[Test]
    public function it_has_utensils_relationship(): void
    {
        $country = Country::factory()->create();

        $this->assertInstanceOf(HasMany::class, $country->utensils());
    }

    #[Test]
    public function it_can_have_many_utensils(): void
    {
        $country = Country::factory()->create();
        Utensil::factory()->count(3)->for($country)->create();

        $this->assertCount(3, $country->utensils);
    }

    #[Test]
    public function it_has_menus_relationship(): void
    {
        $country = Country::factory()->create();

        $this->assertInstanceOf(HasMany::class, $country->menus());
    }

    #[Test]
    public function it_can_have_many_menus(): void
    {
        $country = Country::factory()->create();
        Menu::factory()->count(3)->for($country)->create();

        $this->assertCount(3, $country->menus);
    }

    #[Test]
    public function it_has_favorites_relationship(): void
    {
        $country = Country::factory()->create();

        $this->assertInstanceOf(HasMany::class, $country->favorites());
    }

    #[Test]
    public function it_has_recipe_lists_relationship(): void
    {
        $country = Country::factory()->create();

        $this->assertInstanceOf(HasMany::class, $country->recipeLists());
    }

    #[Test]
    public function it_can_have_many_recipe_lists(): void
    {
        $country = Country::factory()->create();
        RecipeList::factory()->count(3)->forCountry($country)->create();

        $this->assertCount(3, $country->recipeLists);
    }

    #[Test]
    public function it_has_shopping_lists_relationship(): void
    {
        $country = Country::factory()->create();

        $this->assertInstanceOf(HasMany::class, $country->shoppingLists());
    }

    #[Test]
    public function it_can_have_many_shopping_lists(): void
    {
        $country = Country::factory()->create();
        ShoppingList::factory()->count(3)->forCountry($country)->create();

        $this->assertCount(3, $country->shoppingLists);
    }

    #[Test]
    public function it_casts_locales_to_array(): void
    {
        $country = Country::factory()->create([
            'locales' => ['en', 'de'],
        ]);

        $this->assertIsArray($country->locales);
        $this->assertContains('en', $country->locales);
        $this->assertContains('de', $country->locales);
    }

    #[Test]
    public function it_casts_active_to_boolean(): void
    {
        $country = Country::factory()->create(['active' => true]);

        $this->assertTrue($country->active);
    }

    #[Test]
    public function it_casts_integer_fields_correctly(): void
    {
        $country = Country::factory()->create([
            'prep_min' => 10,
            'prep_max' => 30,
            'recipes_count' => 100,
            'ingredients_count' => 50,
            'take' => 20,
        ]);

        $this->assertIsInt($country->prep_min);
        $this->assertIsInt($country->prep_max);
        $this->assertIsInt($country->recipes_count);
        $this->assertIsInt($country->ingredients_count);
        $this->assertIsInt($country->take);
    }

    #[Test]
    public function it_has_active_scope(): void
    {
        Country::factory()->create([
            'active' => true,
            'prep_min' => 10,
            'prep_max' => 30,
            'recipes_count' => 100,
            'ingredients_count' => 50,
        ]);
        Country::factory()->inactive()->create();

        $activeCountries = Country::active()->get();

        $this->assertCount(1, $activeCountries);
    }

    #[Test]
    public function active_scope_requires_prep_min(): void
    {
        Country::factory()->create([
            'active' => true,
            'prep_min' => null,
            'prep_max' => 30,
            'recipes_count' => 100,
            'ingredients_count' => 50,
        ]);

        $activeCountries = Country::active()->get();

        $this->assertCount(0, $activeCountries);
    }

    #[Test]
    public function active_scope_requires_recipes_count(): void
    {
        Country::factory()->create([
            'active' => true,
            'prep_min' => 10,
            'prep_max' => 30,
            'recipes_count' => null,
            'ingredients_count' => 50,
        ]);

        $activeCountries = Country::active()->get();

        $this->assertCount(0, $activeCountries);
    }

    #[Test]
    public function active_scope_requires_ingredients_count(): void
    {
        Country::factory()->create([
            'active' => true,
            'prep_min' => 10,
            'prep_max' => 30,
            'recipes_count' => 100,
            'ingredients_count' => null,
        ]);

        $activeCountries = Country::active()->get();

        $this->assertCount(0, $activeCountries);
    }

    #[Test]
    public function it_hides_sensitive_attributes_on_serialization(): void
    {
        $country = Country::factory()->create();
        $serialized = $country->toArray();

        $this->assertArrayNotHasKey('locales', $serialized);
        $this->assertArrayNotHasKey('domain', $serialized);
        $this->assertArrayNotHasKey('prep_min', $serialized);
        $this->assertArrayNotHasKey('prep_max', $serialized);
        $this->assertArrayNotHasKey('take', $serialized);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        Country::factory()->create([
            'code' => 'US',
        ]);

        $this->assertDatabaseHas('countries', [
            'code' => 'US',
        ]);
    }

    #[Test]
    public function it_can_create_inactive_country(): void
    {
        $country = Country::factory()->inactive()->create();

        $this->assertFalse($country->active);
    }
}
