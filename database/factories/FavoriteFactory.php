<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Favorite;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Favorite>
 */
class FavoriteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = Country::factory()->create();

        return [
            'user_id' => User::factory(),
            'country_id' => $country->id,
            'recipe_id' => Recipe::factory()->for($country),
        ];
    }

    /**
     * Associate the favorite with a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Associate the favorite with a specific country.
     */
    public function forCountry(Country $country): static
    {
        return $this->state(fn (array $attributes): array => [
            'country_id' => $country->id,
        ]);
    }

    /**
     * Associate the favorite with a specific recipe.
     */
    public function forRecipe(Recipe $recipe): static
    {
        return $this->state(fn (array $attributes): array => [
            'recipe_id' => $recipe->id,
            'country_id' => $recipe->country_id,
        ]);
    }
}
