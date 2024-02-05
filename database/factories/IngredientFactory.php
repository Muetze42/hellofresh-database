<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class IngredientFactory extends Factory
{
    protected $model = Ingredient::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->uuid(),
            'uuid' => $this->faker->uuid(),
            'slug' => $this->faker->slug(),
            'type' => $this->faker->word(),
            'country' => $this->faker->country(),
            'image_link' => $this->faker->word(),
            'image_path' => $this->faker->word(),
            'name' => $this->faker->name(),
            'internal_name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'has_duplicated_name' => $this->faker->name(),
            'shipped' => $this->faker->boolean(),
            'usage' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
