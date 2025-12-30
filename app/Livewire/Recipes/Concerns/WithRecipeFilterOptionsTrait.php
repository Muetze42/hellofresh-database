<?php

declare(strict_types=1);

namespace App\Livewire\Recipes\Concerns;

use App\Models\Allergen;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;

/**
 * Provides computed properties for recipe filter options.
 *
 * @property int $countryId
 * @property array<int> $ingredientIds
 * @property array<int> $excludedIngredientIds
 * @property string $ingredientSearch
 * @property string $excludedIngredientSearch
 */
trait WithRecipeFilterOptionsTrait
{
    /**
     * Get available allergens for the current country.
     *
     * @return Collection<int, Allergen>
     */
    #[Computed]
    public function allergens(): Collection
    {
        return Allergen::where('country_id', $this->countryId)
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Get available tags for the current country.
     *
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function tags(): Collection
    {
        return Tag::where('country_id', $this->countryId)
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Get available labels for the current country.
     *
     * @return Collection<int, Label>
     */
    #[Computed]
    public function labels(): Collection
    {
        return Label::where('country_id', $this->countryId)
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Search ingredients for the current country.
     *
     * @return Collection<int, Ingredient>
     */
    #[Computed]
    public function ingredientResults(): Collection
    {
        if ($this->ingredientSearch === '') {
            return new Collection();
        }

        return Ingredient::where('country_id', $this->countryId)
            ->active()
            ->whereLike('name', sprintf('%%%s%%', $this->ingredientSearch))
            ->whereNotIn('id', $this->ingredientIds)
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    /**
     * Get selected ingredients.
     *
     * @return Collection<int, Ingredient>
     */
    #[Computed]
    public function selectedIngredients(): Collection
    {
        if ($this->ingredientIds === []) {
            return new Collection();
        }

        return Ingredient::whereIn('id', $this->ingredientIds)->get();
    }

    /**
     * Search ingredients to exclude for the current country.
     *
     * @return Collection<int, Ingredient>
     */
    #[Computed]
    public function excludedIngredientResults(): Collection
    {
        if ($this->excludedIngredientSearch === '') {
            return new Collection();
        }

        return Ingredient::where('country_id', $this->countryId)
            ->active()
            ->whereLike('name', sprintf('%%%s%%', $this->excludedIngredientSearch))
            ->whereNotIn('id', $this->excludedIngredientIds)
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    /**
     * Get selected excluded ingredients.
     *
     * @return Collection<int, Ingredient>
     */
    #[Computed]
    public function selectedExcludedIngredients(): Collection
    {
        if ($this->excludedIngredientIds === []) {
            return new Collection();
        }

        return Ingredient::whereIn('id', $this->excludedIngredientIds)->get();
    }
}
