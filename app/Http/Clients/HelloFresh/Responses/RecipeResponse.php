<?php

namespace App\Http\Clients\HelloFresh\Responses;

/**
 * @phpstan-import-type Recipe from RecipesResponse
 *
 * @extends AbstractHelloFreshResponse<Recipe>
 */
class RecipeResponse extends AbstractHelloFreshResponse
{
    /**
     * Get the JSON decoded body of the response as an array.
     *
     * @return Recipe
     */
    public function array(): array
    {
        return $this->toArray();
    }

    /**
     * Get the recipe ID.
     */
    public function id(): string
    {
        return $this->array()['id'];
    }

    /**
     * Get the recipe name.
     */
    public function name(): string
    {
        return $this->array()['name'];
    }

    /**
     * Get the recipe slug.
     */
    public function slug(): string
    {
        return $this->array()['slug'];
    }
}
