<?php

namespace Database\Factories;

use App\Models\Utensil;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UtensilFactory extends Factory
{
    protected $model = Utensil::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->uuid(),
            'type' => $this->faker->word(),
            'name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
