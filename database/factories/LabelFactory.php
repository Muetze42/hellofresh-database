<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Label;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Label>
 */
class LabelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'handles' => [fake()->unique()->slug(2)],
            'country_id' => Country::factory(),
            'name' => ['en' => fake()->randomElement(['New', 'Premium', 'Family Friendly', 'Quick', 'Low Calorie'])],
            'foreground_color' => fake()->hexColor(),
            'background_color' => fake()->hexColor(),
            'display_label' => fake()->boolean(),
            'active' => true,
        ];
    }

    /**
     * Indicate that the label is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'active' => false,
        ]);
    }

    /**
     * Indicate that the label should be displayed.
     */
    public function displayLabel(): static
    {
        return $this->state(fn (array $attributes): array => [
            'display_label' => true,
        ]);
    }
}
