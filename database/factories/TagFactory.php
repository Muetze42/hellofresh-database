<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->uuid(),
            'type' => $this->faker->word(),
            'name' => $this->faker->name(),
            'color_handle' => $this->faker->word(),
            'preferences' => $this->faker->words(),
            'display_label' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
