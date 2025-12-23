<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Cuisine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cuisine>
 */
class CuisineFactory extends Factory
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
            'name' => ['en' => fake()->randomElement(['Italian', 'Mexican', 'Asian', 'American', 'Mediterranean', 'Indian'])],
            'icon_path' => null,
            'active' => true,
        ];
    }

    /**
     * Indicate that the cuisine is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'active' => false,
        ]);
    }
}
