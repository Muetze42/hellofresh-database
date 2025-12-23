<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_id' => Country::factory(),
            'name' => ['en' => fake()->word()],
            'hellofresh_ids' => [fake()->uuid()],
            'image_path' => null,
            'active' => true,
        ];
    }

    /**
     * Indicate that the ingredient is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'active' => false,
        ]);
    }
}
