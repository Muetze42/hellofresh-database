<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Utensil;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Utensil>
 */
class UtensilFactory extends Factory
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
            'name' => ['en' => fake()->randomElement(['Pan', 'Pot', 'Baking Sheet', 'Cutting Board', 'Knife', 'Whisk'])],
            'hellofresh_ids' => [fake()->uuid()],
        ];
    }
}
