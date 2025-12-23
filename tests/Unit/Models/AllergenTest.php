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

final class AllergenTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_country_relationship(): void
    {
        $allergen = Allergen::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $allergen->country());
        $this->assertInstanceOf(Country::class, $allergen->country);
    }

    #[Test]
    public function it_has_recipes_relationship(): void
    {
        $allergen = Allergen::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $allergen->recipes());
    }

    #[Test]
    public function it_can_belong_to_many_recipes(): void
    {
        $country = Country::factory()->create();
        $allergen = Allergen::factory()->for($country)->create();
        $recipes = Recipe::factory()->count(3)->for($country)->create();

        $allergen->recipes()->attach($recipes);

        $this->assertCount(3, $allergen->recipes);
    }

    #[Test]
    public function it_has_ingredients_relationship(): void
    {
        $allergen = Allergen::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $allergen->ingredients());
    }

    #[Test]
    public function it_can_have_many_ingredients(): void
    {
        $country = Country::factory()->create();
        $allergen = Allergen::factory()->for($country)->create();
        $ingredients = Ingredient::factory()->count(2)->for($country)->create();

        $allergen->ingredients()->attach($ingredients);

        $this->assertCount(2, $allergen->ingredients);
    }

    #[Test]
    public function it_has_translatable_name(): void
    {
        $allergen = new Allergen();

        $this->assertContains('name', $allergen->translatable);
    }

    #[Test]
    public function it_hides_sensitive_attributes_on_serialization(): void
    {
        $allergen = Allergen::factory()->create();
        $serialized = $allergen->toArray();

        $this->assertArrayNotHasKey('hellofresh_ids', $serialized);
        $this->assertArrayNotHasKey('slug', $serialized);
        $this->assertArrayNotHasKey('type', $serialized);
        $this->assertArrayNotHasKey('icon_path', $serialized);
    }

    #[Test]
    public function it_has_active_scope(): void
    {
        $country = Country::factory()->create();
        Allergen::factory()->for($country)->create(['active' => true]);
        Allergen::factory()->for($country)->create(['active' => false]);

        $activeAllergens = Allergen::active()->get();

        $this->assertCount(1, $activeAllergens);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        $allergen = Allergen::factory()->create([
            'name' => ['en' => 'Gluten'],
        ]);

        $this->assertDatabaseHas('allergens', [
            'id' => $allergen->id,
        ]);
    }

    #[Test]
    public function it_can_create_inactive_allergen(): void
    {
        $allergen = Allergen::factory()->inactive()->create();

        $this->assertFalse($allergen->active);
    }
}
