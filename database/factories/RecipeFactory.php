<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Family;
use App\Models\Label;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    public function definition(): array
    {
        return [
            'external_id' => $this->faker->word(),
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'canonical' => $this->faker->word(),
            'canonical_link' => $this->faker->word(),
            'card_link' => $this->faker->word(),
            'cloned_from' => $this->faker->word(),
            'headline' => $this->faker->word(),
            'image_link' => $this->faker->word(),
            'image_path' => $this->faker->word(),
            'total_time' => $this->faker->word(),
            'prep_time' => $this->faker->word(),
            'country' => $this->faker->country(),
            'comment' => $this->faker->word(),
            'description' => $this->faker->text(),
            'description_markdown' => $this->faker->text(),
            'average_rating' => $this->faker->randomNumber(),
            'favorites_count' => $this->faker->randomNumber(),
            'ratings_count' => $this->faker->randomNumber(),
            'serving_size' => $this->faker->randomNumber(),
            'difficulty' => $this->faker->randomNumber(),
            'active' => $this->faker->boolean(),
            'is_addon' => $this->faker->boolean(),
            'nutrition' => $this->faker->words(),
            'steps' => $this->faker->words(),
            'yields' => $this->faker->words(),
            'external_created_at' => $this->faker->randomNumber(),
            'external_updated_at' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'label_id' => Label::factory(),
            'category_id' => Category::factory(),
            'family_id' => Family::factory(),
        ];
    }
}
