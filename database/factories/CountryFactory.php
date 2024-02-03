<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'country' => $this->faker->country(),
            'locale' => $this->faker->word(),
            'domain' => $this->faker->word(),
            'data' => $this->faker->words(),
            'take' => $this->faker->randomNumber(),
            'recipes' => $this->faker->randomNumber(),
            'ingredients' => $this->faker->randomNumber(),
            'active' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
