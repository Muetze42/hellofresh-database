<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hellofresh_id' => fake()->uuid(),
            'country_id' => Country::factory(),
            'name' => ['en' => fake()->words(3, true)],
            'headline' => ['en' => fake()->sentence()],
            'description' => ['en' => fake()->paragraph()],
            'difficulty' => fake()->numberBetween(1, 3),
            'prep_time' => fake()->numberBetween(5, 30),
            'total_time' => fake()->numberBetween(15, 60),
            'image_path' => null,
            'card_link' => null,
            'steps_primary' => [
                ['index' => 1, 'instructions' => fake()->sentence(), 'images' => []],
                ['index' => 2, 'instructions' => fake()->sentence(), 'images' => []],
            ],
            'steps_secondary' => null,
            'nutrition_primary' => [
                ['name' => 'Calories', 'amount' => fake()->numberBetween(300, 800), 'unit' => 'kcal'],
                ['name' => 'Protein', 'amount' => fake()->numberBetween(10, 50), 'unit' => 'g'],
                ['name' => 'Fat', 'amount' => fake()->numberBetween(5, 40), 'unit' => 'g'],
            ],
            'nutrition_secondary' => null,
            'yields_primary' => [
                ['yields' => 2, 'ingredients' => []],
                ['yields' => 4, 'ingredients' => []],
            ],
            'yields_secondary' => null,
            'has_pdf' => false,
        ];
    }

    /**
     * Indicate that the recipe is a variant of another recipe.
     */
    public function variant(Recipe $canonical): static
    {
        return $this->state(fn (array $attributes): array => [
            'canonical_id' => $canonical->id,
            'country_id' => $canonical->country_id,
        ]);
    }

    /**
     * Indicate that the recipe has a PDF available.
     */
    public function withPdf(): static
    {
        return $this->state(fn (array $attributes): array => [
            'has_pdf' => true,
            'card_link' => ['en' => fake()->url()],
        ]);
    }

    /**
     * Indicate that the recipe is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'active' => true,
        ]);
    }

    /**
     * Indicate that the recipe is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'active' => false,
        ]);
    }
}
