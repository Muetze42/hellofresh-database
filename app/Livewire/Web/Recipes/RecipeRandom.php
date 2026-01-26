<?php

declare(strict_types=1);

namespace App\Livewire\Web\Recipes;

use App\Enums\FilterSharePageEnum;
use App\Models\Recipe;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Override;

class RecipeRandom extends RecipeIndex
{
    /**
     * Get random recipes based on current filters.
     *
     * @return Collection<int, Recipe>
     */
    #[Computed]
    public function randomRecipes(): Collection
    {
        return Recipe::where('country_id', $this->countryId)
            ->when($this->search !== '', fn (Builder $query): Builder => $this->applySearchFilter($query))
            ->when($this->filterHasPdf, fn (Builder $query) => $query->where('has_pdf', true))
            ->when($this->filterHideVariants, fn (Builder $query) => $query->where('variant', false))
            ->when($this->excludedAllergenIds !== [], fn (Builder $query) => $query->whereDoesntHave(
                'allergens',
                fn (Builder $allergenQuery) => $allergenQuery->whereIn('allergens.id', $this->excludedAllergenIds)
            ))
            ->when($this->ingredientIds !== [], fn (Builder $query): Builder => $this->applyIngredientFilter($query))
            ->when($this->excludedIngredientIds !== [], fn (Builder $query) => $query->whereDoesntHave(
                'ingredients',
                fn (Builder $ingredientQuery) => $ingredientQuery->whereIn('ingredients.id', $this->excludedIngredientIds)
            ))
            ->when($this->tagIds !== [], fn (Builder $query) => $query->whereHas(
                'tags',
                fn (Builder $tagQuery) => $tagQuery->whereIn('tags.id', $this->tagIds)
            ))
            ->when($this->excludedTagIds !== [], fn (Builder $query) => $query->whereDoesntHave(
                'tags',
                fn (Builder $tagQuery) => $tagQuery->whereIn('tags.id', $this->excludedTagIds)
            ))
            ->when($this->labelIds !== [], fn (Builder $query) => $query->whereIn('label_id', $this->labelIds))
            ->when($this->excludedLabelIds !== [], fn (Builder $query) => $query->whereNotIn('label_id', $this->excludedLabelIds))
            ->when($this->difficultyLevels !== [], fn (Builder $query) => $query->whereIn('difficulty', $this->difficultyLevels))
            ->when($this->isPrepTimeFilterActive(), fn (Builder $query): Builder => $this->applyPrepTimeFilter($query))
            ->when($this->isTotalTimeFilterActive(), fn (Builder $query): Builder => $this->applyTotalTimeFilter($query))
            ->with(['country', 'label', 'tags'])
            ->inRandomOrder()
            ->limit($this->perPage)
            ->get();
    }

    /**
     * Shuffle the recipes by clearing the computed cache.
     */
    public function shuffle(): void
    {
        unset($this->randomRecipes);
    }

    /**
     * Toggle a tag filter and refresh random recipes.
     */
    #[Override]
    public function toggleTag(int $id): void
    {
        parent::toggleTag($id);
        unset($this->randomRecipes);
    }

    /**
     * Get the filter share page enum for this component.
     */
    #[Override]
    protected function getFilterSharePage(): FilterSharePageEnum
    {
        return FilterSharePageEnum::Random;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    #[Override]
    public function render(): ViewInterface
    {
        $title = page_title(__('Random Recipes'));

        return view('web::livewire.recipes.recipe-random')
            ->title($title)
            ->layoutData([
                'ogTitle' => __('Random Recipes'),
                'ogDescription' => __('Discover random HelloFresh recipes. Filter by difficulty, cooking time, allergens and more.'),
            ]);
    }
}
