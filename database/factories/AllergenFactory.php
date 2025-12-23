<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Allergen;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Allergen>
 */
class AllergenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hellofresh_ids' => [fake()->uuid()],
            'country_id' => Country::factory(),
            'name' => ['en' => fake()->randomElement(['Gluten', 'Dairy', 'Nuts', 'Eggs', 'Soy', 'Fish', 'Shellfish'])],
            'icon_path' => null,
            'active' => true,
        ];
    }

    /**
     * Indicate that the allergen is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'active' => false,
        ]);
    }
}
