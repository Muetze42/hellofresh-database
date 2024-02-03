<?php

namespace Database\Factories;

use App\Models\Cuisine;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CuisineFactory extends Factory
{
    protected $model = Cuisine::class;

    public function definition(): array
    {
        return [
            'external_id' => $this->faker->unique()->uuid(),
            'type' => $this->faker->word(),
            'name' => $this->faker->name(),
            'icon_link' => $this->faker->word(),
            'icon_path' => $this->faker->word(),
            'usage' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
