<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\Recipe;
use App\Models\Utensil;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UtensilTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_country_relationship(): void
    {
        $utensil = Utensil::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $utensil->country());
        $this->assertInstanceOf(Country::class, $utensil->country);
    }

    #[Test]
    public function it_has_recipes_relationship(): void
    {
        $utensil = Utensil::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $utensil->recipes());
    }

    #[Test]
    public function it_can_belong_to_many_recipes(): void
    {
        $country = Country::factory()->create();
        $utensil = Utensil::factory()->for($country)->create();
        $recipes = Recipe::factory()->count(3)->for($country)->create();

        $utensil->recipes()->attach($recipes);

        $this->assertCount(3, $utensil->recipes);
    }

    #[Test]
    public function it_has_translatable_name(): void
    {
        $utensil = new Utensil();

        $this->assertContains('name', $utensil->translatable);
    }

    #[Test]
    public function it_hides_sensitive_attributes_on_serialization(): void
    {
        $utensil = Utensil::factory()->create();
        $serialized = $utensil->toArray();

        $this->assertArrayNotHasKey('hellofresh_ids', $serialized);
        $this->assertArrayNotHasKey('type', $serialized);
    }

    #[Test]
    public function it_has_active_scope(): void
    {
        $country = Country::factory()->create();
        Utensil::factory()->for($country)->create(['active' => true]);
        Utensil::factory()->for($country)->create(['active' => false]);

        $activeUtensils = Utensil::active()->get();

        $this->assertCount(1, $activeUtensils);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        $utensil = Utensil::factory()->create([
            'name' => ['en' => 'Pan'],
        ]);

        $this->assertDatabaseHas('utensils', [
            'id' => $utensil->id,
        ]);
    }
}
