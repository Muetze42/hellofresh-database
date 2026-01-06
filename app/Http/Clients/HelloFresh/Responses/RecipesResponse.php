<?php

namespace App\Http\Clients\HelloFresh\Responses;

/**
 * @phpstan-type RecipeAllergen array{
 *     id: string,
 *     name: string,
 *     type: string,
 *     slug: string,
 *     iconLink: string|null,
 *     iconPath: string|null,
 *     triggersTracesOf: bool,
 *     tracesOf: bool
 * }
 * @phpstan-type RecipeCuisine array{
 *     id: string,
 *     type: string,
 *     name: string,
 *     slug: string,
 *     iconLink: string|null
 * }
 * @phpstan-type RecipeTag array{
 *     id: string,
 *     type: string,
 *     name: string,
 *     slug: string,
 *     colorHandle: string|null,
 *     preferences: list<string>,
 *     displayLabel: bool
 * }
 * @phpstan-type IngredientFamily array{
 *     id: string,
 *     uuid: string,
 *     name: string,
 *     slug: string,
 *     type: string,
 *     priority: int,
 *     iconLink: string|null,
 *     iconPath: string|null
 * }
 * @phpstan-type RecipeIngredient array{
 *     id: string,
 *     uuid: string,
 *     name: string,
 *     type: string,
 *     slug: string,
 *     country: string,
 *     imageLink: string|null,
 *     imagePath: string|null,
 *     shipped: bool,
 *     allergens: list<string>,
 *     family: IngredientFamily
 * }
 * @phpstan-type RecipeNutrition array{
 *     type: string,
 *     name: string,
 *     amount: float|int,
 *     unit: string
 * }
 * @phpstan-type StepImage array{
 *     link: string,
 *     path: string,
 *     caption: string
 * }
 * @phpstan-type RecipeStep array{
 *     index: int,
 *     instructions: string,
 *     instructionsHTML: string,
 *     instructionsMarkdown: string,
 *     ingredients: list<string>,
 *     utensils: list<string>,
 *     timers: list<mixed>,
 *     images: list<StepImage>,
 *     videos: list<mixed>
 * }
 * @phpstan-type RecipeUtensil array{
 *     id: string,
 *     type: string|null,
 *     name: string
 * }
 * @phpstan-type YieldIngredient array{
 *     id: string,
 *     amount: float|int|null,
 *     unit: string
 * }
 * @phpstan-type RecipeYield array{
 *     yields: int,
 *     ingredients: list<YieldIngredient>
 * }
 * @phpstan-type RecipeLabel array{
 *     text: string,
 *     handle: string,
 *     foregroundColor: string,
 *     backgroundColor: string,
 *     displayLabel: bool
 * }
 * @phpstan-type Recipe array{
 *     id: string,
 *     uuid: string,
 *     name: string,
 *     slug: string,
 *     headline: string,
 *     description: string,
 *     descriptionHTML: string,
 *     descriptionMarkdown: string,
 *     difficulty: int,
 *     prepTime: string,
 *     totalTime: string,
 *     imageLink: string,
 *     imagePath: string,
 *     country: string,
 *     canonical: string,
 *     canonicalLink: string,
 *     websiteUrl: string,
 *     link: string,
 *     averageRating: float|int,
 *     ratingsCount: int,
 *     favoritesCount: int,
 *     active: bool,
 *     isAddon: bool,
 *     isComplete: bool|null,
 *     isPublished: bool,
 *     createdAt: string,
 *     updatedAt: string,
 *     uniqueRecipeCode: string,
 *     servingSize: int,
 *     clonedFrom: string|null,
 *     cardLink: string|null,
 *     category: string|null,
 *     comment: string|null,
 *     label: RecipeLabel|null,
 *     promotion: string|null,
 *     seoDescription: string|null,
 *     seoName: string|null,
 *     videoLink: string|null,
 *     allergens: list<RecipeAllergen>,
 *     cuisines: list<RecipeCuisine>,
 *     tags: list<RecipeTag>,
 *     ingredients: list<RecipeIngredient>,
 *     nutrition: list<RecipeNutrition>,
 *     steps: list<RecipeStep>,
 *     utensils: list<RecipeUtensil>,
 *     yields: list<RecipeYield>
 * }
 * @phpstan-type RecipesData array{
 *     items: list<Recipe>,
 *     take: int,
 *     skip: int,
 *     count: int,
 *     total: int
 * }
 *
 * @extends AbstractHelloFreshResponse<RecipesData>
 */
class RecipesResponse extends AbstractHelloFreshResponse
{
    /**
     * Get the JSON decoded body of the response as an array.
     *
     * @return RecipesData
     */
    public function array(): array
    {
        return $this->toArray();
    }

    /**
     * Get the recipe items.
     *
     * @return list<Recipe>
     */
    public function items(): array
    {
        return $this->array()['items'];
    }

    /**
     * Get the total number of recipes available.
     */
    public function total(): int
    {
        return $this->array()['total'];
    }

    /**
     * Get the number of items taken in this response.
     */
    public function take(): int
    {
        return $this->array()['take'];
    }

    /**
     * Get the number of items skipped.
     */
    public function skip(): int
    {
        return $this->array()['skip'];
    }

    /**
     * Check if there are more pages available.
     */
    public function hasMorePages(): bool
    {
        $data = $this->array();

        return ($data['skip'] + $data['take']) < $data['total'];
    }

    /**
     * Get the skip value for the next page.
     */
    public function nextSkip(): int
    {
        $data = $this->array();

        return $data['skip'] + $data['take'];
    }
}
