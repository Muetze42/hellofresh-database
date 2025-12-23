<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Concerns;

use App\Models\Country;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HasHelloFreshIdsTraitTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_adds_hellofresh_ids_to_fillable(): void
    {
        $ingredient = new Ingredient();
        $fillable = $ingredient->getFillable();

        $this->assertContains('hellofresh_ids', $fillable);
    }

    #[Test]
    public function it_casts_hellofresh_ids_to_array(): void
    {
        $ingredient = new Ingredient();
        $casts = $ingredient->getCasts();

        $this->assertArrayHasKey('hellofresh_ids', $casts);
        $this->assertSame('array', $casts['hellofresh_ids']);
    }

    #[Test]
    public function update_or_create_creates_new_model_when_not_found(): void
    {
        $country = Country::factory()->create();
        $hellofreshId = 'new-id-123';

        $ingredient = Ingredient::updateOrCreateByHelloFreshId(
            $country->ingredients(),
            $hellofreshId,
            'en',
            ['name' => 'New Ingredient']
        );

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
        ]);
        $this->assertContains($hellofreshId, $ingredient->hellofresh_ids);
    }

    #[Test]
    public function update_or_create_finds_by_hellofresh_id(): void
    {
        $country = Country::factory()->create();
        $hellofreshId = 'existing-id-123';
        $existingIngredient = Ingredient::factory()->for($country)->create([
            'hellofresh_ids' => [$hellofreshId],
            'name' => ['en' => 'Original Name'],
        ]);

        $updatedIngredient = Ingredient::updateOrCreateByHelloFreshId(
            $country->ingredients(),
            $hellofreshId,
            'en',
            ['name' => 'Updated Name']
        );

        $this->assertTrue($updatedIngredient->is($existingIngredient));
    }

    #[Test]
    public function update_or_create_finds_by_name_when_hellofresh_id_not_found(): void
    {
        $country = Country::factory()->create();
        $existingIngredient = Ingredient::factory()->for($country)->create([
            'hellofresh_ids' => ['different-id'],
            'name' => ['en' => 'Existing Name'],
        ]);

        $updatedIngredient = Ingredient::updateOrCreateByHelloFreshId(
            $country->ingredients(),
            'new-hellofresh-id',
            'en',
            ['name' => 'Existing Name']
        );

        $this->assertTrue($updatedIngredient->is($existingIngredient));
        $this->assertContains('new-hellofresh-id', $updatedIngredient->fresh()->hellofresh_ids);
    }

    #[Test]
    public function add_hellofresh_id_adds_new_id(): void
    {
        $country = Country::factory()->create();
        $ingredient = Ingredient::factory()->for($country)->create([
            'hellofresh_ids' => ['id-1'],
        ]);

        $ingredient->addHelloFreshId('id-2');

        $this->assertContains('id-1', $ingredient->fresh()->hellofresh_ids);
        $this->assertContains('id-2', $ingredient->fresh()->hellofresh_ids);
    }

    #[Test]
    public function add_hellofresh_id_does_not_duplicate_existing_id(): void
    {
        $country = Country::factory()->create();
        $ingredient = Ingredient::factory()->for($country)->create([
            'hellofresh_ids' => ['id-1'],
        ]);

        $ingredient->addHelloFreshId('id-1');

        $this->assertCount(1, $ingredient->fresh()->hellofresh_ids);
    }

    #[Test]
    public function add_hellofresh_id_handles_null_hellofresh_ids(): void
    {
        $country = Country::factory()->create();
        $ingredient = Ingredient::factory()->for($country)->create([
            'hellofresh_ids' => null,
        ]);

        $ingredient->addHelloFreshId('new-id');

        $this->assertContains('new-id', $ingredient->fresh()->hellofresh_ids);
    }
}
