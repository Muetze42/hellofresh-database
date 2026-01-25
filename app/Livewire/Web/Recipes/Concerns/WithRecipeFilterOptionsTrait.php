<?php

declare(strict_types=1);

namespace App\Livewire\Web\Recipes\Concerns;

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
 * @property Collection<int, Ingredient> $ingredientOptions
 * @property Collection<int, Ingredient> $excludedIngredientOptions
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
     * Get ingredient options (selected + search results).
     *
     * @return Collection<int, Ingredient>
     */
    #[Computed]
    public function ingredientOptions(): Collection
    {
        $selected = $this->ingredientIds !== []
            ? Ingredient::whereIn('id', $this->ingredientIds)->get()
            : new Collection();

        if ($this->ingredientSearch === '') {
            return $selected;
        }

        $results = Ingredient::where('country_id', $this->countryId)
            ->active()
            ->whereLike('name', sprintf('%%%s%%', $this->ingredientSearch))
            ->whereNotIn('id', $this->ingredientIds)
            ->orderBy('name')
            ->limit(20)
            ->get();

        return $selected->concat($results);
    }

    /**
     * Get excluded ingredient options (selected + search results).
     *
     * @return Collection<int, Ingredient>
     */
    #[Computed]
    public function excludedIngredientOptions(): Collection
    {
        $selected = $this->excludedIngredientIds !== []
            ? Ingredient::whereIn('id', $this->excludedIngredientIds)->get()
            : new Collection();

        if ($this->excludedIngredientSearch === '') {
            return $selected;
        }

        $results = Ingredient::where('country_id', $this->countryId)
            ->active()
            ->whereLike('name', sprintf('%%%s%%', $this->excludedIngredientSearch))
            ->whereNotIn('id', $this->excludedIngredientIds)
            ->orderBy('name')
            ->limit(20)
            ->get();

        return $selected->concat($results);
    }

    /**
     * Check if there are search results for ingredients (excluding selected).
     */
    public function hasIngredientSearchResults(): bool
    {
        return $this->ingredientSearch !== '' &&
            $this->ingredientOptions->count() > count($this->ingredientIds);
    }

    /**
     * Check if there are search results for excluded ingredients (excluding selected).
     */
    public function hasExcludedIngredientSearchResults(): bool
    {
        return $this->excludedIngredientSearch !== '' &&
            $this->excludedIngredientOptions->count() > count($this->excludedIngredientIds);
    }
}
