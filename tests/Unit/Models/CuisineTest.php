<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\Cuisine;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CuisineTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_country_relationship(): void
    {
        $cuisine = Cuisine::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $cuisine->country());
        $this->assertInstanceOf(Country::class, $cuisine->country);
    }

    #[Test]
    public function it_has_recipes_relationship(): void
    {
        $cuisine = Cuisine::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $cuisine->recipes());
    }

    #[Test]
    public function it_can_belong_to_many_recipes(): void
    {
        $country = Country::factory()->create();
        $cuisine = Cuisine::factory()->for($country)->create();
        $recipes = Recipe::factory()->count(3)->for($country)->create();

        $cuisine->recipes()->attach($recipes);

        $this->assertCount(3, $cuisine->recipes);
    }

    #[Test]
    public function it_has_translatable_name(): void
    {
        $cuisine = new Cuisine();

        $this->assertContains('name', $cuisine->translatable);
    }

    #[Test]
    public function it_hides_sensitive_attributes_on_serialization(): void
    {
        $cuisine = Cuisine::factory()->create();
        $serialized = $cuisine->toArray();

        $this->assertArrayNotHasKey('hellofresh_ids', $serialized);
        $this->assertArrayNotHasKey('icon_path', $serialized);
    }

    #[Test]
    public function it_has_active_scope(): void
    {
        $country = Country::factory()->create();
        Cuisine::factory()->for($country)->create(['active' => true]);
        Cuisine::factory()->for($country)->create(['active' => false]);

        $activeCuisines = Cuisine::active()->get();

        $this->assertCount(1, $activeCuisines);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        $cuisine = Cuisine::factory()->create([
            'name' => ['en' => 'Italian'],
        ]);

        $this->assertDatabaseHas('cuisines', [
            'id' => $cuisine->id,
        ]);
    }

    #[Test]
    public function it_can_create_inactive_cuisine(): void
    {
        $cuisine = Cuisine::factory()->inactive()->create();

        $this->assertFalse($cuisine->active);
    }
}
