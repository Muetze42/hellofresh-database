<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShoppingList>
 */
class ShoppingListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'country_id' => Country::factory(),
            'name' => fake()->words(3, true),
            'items' => [],
        ];
    }

    /**
     * Associate the shopping list with a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Associate the shopping list with a specific country.
     */
    public function forCountry(Country $country): static
    {
        return $this->state(fn (array $attributes): array => [
            'country_id' => $country->id,
        ]);
    }

    /**
     * Create a shopping list with items.
     *
     * @param  array<int, array{recipe_id: int, servings: int}>  $items
     */
    public function withItems(array $items): static
    {
        return $this->state(fn (array $attributes): array => [
            'items' => $items,
        ]);
    }
}
