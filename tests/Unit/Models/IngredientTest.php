<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Allergen;
use App\Models\Country;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class IngredientTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_country_relationship(): void
    {
        $ingredient = Ingredient::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $ingredient->country());
        $this->assertInstanceOf(Country::class, $ingredient->country);
    }

    #[Test]
    public function it_has_recipes_relationship(): void
    {
        $ingredient = Ingredient::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $ingredient->recipes());
    }

    #[Test]
    public function it_can_belong_to_many_recipes(): void
    {
        $country = Country::factory()->create();
        $ingredient = Ingredient::factory()->for($country)->create();
        $recipes = Recipe::factory()->count(3)->for($country)->create();

        $ingredient->recipes()->attach($recipes);

        $this->assertCount(3, $ingredient->recipes);
    }

    #[Test]
    public function it_has_allergens_relationship(): void
    {
        $ingredient = Ingredient::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $ingredient->allergens());
    }

    #[Test]
    public function it_can_have_many_allergens(): void
    {
        $country = Country::factory()->create();
        $ingredient = Ingredient::factory()->for($country)->create();
        $allergens = Allergen::factory()->count(2)->for($country)->create();

        $ingredient->allergens()->attach($allergens);

        $this->assertCount(2, $ingredient->allergens);
    }

    #[Test]
    public function it_has_translatable_name(): void
    {
        $ingredient = new Ingredient();

        $this->assertContains('name', $ingredient->translatable);
    }

    #[Test]
    public function it_hides_sensitive_attributes_on_serialization(): void
    {
        $ingredient = Ingredient::factory()->create();
        $serialized = $ingredient->toArray();

        $this->assertArrayNotHasKey('hellofresh_ids', $serialized);
        $this->assertArrayNotHasKey('image_path', $serialized);
    }

    #[Test]
    public function it_has_active_scope(): void
    {
        $country = Country::factory()->create();
        Ingredient::factory()->for($country)->create(['active' => true]);
        Ingredient::factory()->for($country)->create(['active' => false]);

        $activeIngredients = Ingredient::active()->get();

        $this->assertCount(1, $activeIngredients);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => ['en' => 'Tomato'],
        ]);

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
        ]);
    }
}
