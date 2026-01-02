<?php

namespace App\Livewire\Portal;

use App\Models\Country;
use App\Models\Recipe;
use App\Models\RecipeList;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
class RecipeListShow extends Component
{
    public RecipeList $recipeList;

    public function mount(RecipeList $recipeList): void
    {
        $user = auth()->user();

        abort_if(! $user || ! $recipeList->isAccessibleBy($user), 403);

        $this->recipeList = $recipeList->load(['recipes.country']);
    }

    /**
     * Get recipes grouped by country.
     *
     * @return Collection<int, array{country: Country|null, recipes: Collection<int, Recipe>}>
     */
    #[Computed]
    public function recipesByCountry(): Collection
    {
        /** @var Collection<int, Collection<int, Recipe>> $grouped */
        $grouped = $this->recipeList->recipes->groupBy(fn (Recipe $recipe): int => (int) $recipe->getAttribute('pivot')?->country_id);

        return $grouped->map(function (Collection $recipes, int $countryId): array {
            $country = Country::find($countryId);

            return [
                'country' => $country,
                'recipes' => $recipes,
            ];
        });
    }

    /**
     * Remove a recipe from the list.
     */
    public function removeRecipe(int $recipeId): void
    {
        $user = auth()->user();

        if (! $user || ! $this->recipeList->isOwnedBy($user)) {
            return;
        }

        $this->recipeList->recipes()->detach($recipeId);
        $this->recipeList->load(['recipes.country']);

        unset($this->recipesByCountry);

        Flux::toastSuccess(__('Recipe removed from list.'));
    }

    public function render(): View
    {
        return view('portal::livewire.recipe-list-show')
            ->title($this->recipeList->name . ' - ' . __('My Recipe Lists'));
    }
}
