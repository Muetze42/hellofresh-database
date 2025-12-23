<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->countryCode(),
            'domain' => fake()->domainName(),
            'locales' => ['en'],
            'prep_min' => fake()->numberBetween(5, 15),
            'prep_max' => fake()->numberBetween(30, 60),
            'recipes_count' => fake()->numberBetween(100, 1000),
            'ingredients_count' => fake()->numberBetween(50, 500),
            'take' => fake()->numberBetween(10, 100),
            'active' => true,
        ];
    }

    /**
     * Indicate that the country is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'active' => false,
        ]);
    }
}
