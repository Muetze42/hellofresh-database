<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
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
            'name' => ['en' => fake()->word()],
            'active' => true,
            'display_label' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the tag is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'active' => false,
        ]);
    }

    /**
     * Indicate that the tag should display a label.
     */
    public function displayLabel(): static
    {
        return $this->state(fn (array $attributes): array => [
            'display_label' => true,
        ]);
    }
}
