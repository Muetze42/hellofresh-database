<?php

namespace Database\Factories;

use App\Models\Family;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FamilyFactory extends Factory
{
    protected $model = Family::class;

    public function definition(): array
    {
        return [
            'external_id' => $this->faker->unique()->uuid(),
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'type' => $this->faker->word(),
            'icon_link' => $this->faker->word(),
            'icon_path' => $this->faker->word(),
            'description' => $this->faker->text(),
            'usage_by_country' => $this->faker->country(),
            'priority' => $this->faker->randomNumber(),
            'external_created_at' => Carbon::now(),
            'external_updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
