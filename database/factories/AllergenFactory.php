<?php

namespace Database\Factories;

use App\Models\Allergen;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AllergenFactory extends Factory
{
    protected $model = Allergen::class;

    public function definition(): array
    {
        return [
            'external_id' => $this->faker->unique()->uuid(),
            'name' => $this->faker->name(),
            'type' => $this->faker->word(),
            'icon_path' => $this->faker->word(),
            'description' => $this->faker->text(),
            'triggers_traces_of' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
