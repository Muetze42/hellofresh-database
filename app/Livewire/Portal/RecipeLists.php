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
class RecipeLists extends Component
{
    public ?int $viewingListId = null;

    /**
     * Get the user's recipe lists.
     *
     * @return Collection<int, RecipeList>
     */
    #[Computed]
    public function recipeLists(): Collection
    {
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        return RecipeList::where('user_id', $user->id)
            ->withCount('recipes')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the currently viewed list with ALL recipes (no country filter).
     */
    #[Computed]
    public function viewingList(): ?RecipeList
    {
        if (! $this->viewingListId) {
            return null;
        }

        return RecipeList::with(['recipes.country'])
            ->find($this->viewingListId);
    }

    /**
     * Get recipes grouped by country for the viewing list.
     *
     * @return Collection<int, array{country: Country|null, recipes: Collection<int, Recipe>}>
     */
    #[Computed]
    public function recipesByCountry(): Collection
    {
        $list = $this->viewingList();

        if (! $list instanceof RecipeList) {
            return collect();
        }

        /** @var Collection<int, Collection<int, Recipe>> $grouped */
        $grouped = $list->recipes->groupBy(fn (Recipe $recipe): int => (int) $recipe->getAttribute('pivot')?->country_id);

        return $grouped->map(function (Collection $recipes, int $countryId): array {
            $country = Country::find($countryId);

            return [
                'country' => $country,
                'recipes' => $recipes,
            ];
        });
    }

    /**
     * View a list's recipes.
     */
    public function viewList(int $listId): void
    {
        $this->viewingListId = $listId;
        unset($this->viewingList, $this->recipesByCountry);
    }

    /**
     * Go back to list overview.
     */
    public function backToLists(): void
    {
        $this->viewingListId = null;
    }

    /**
     * Remove a recipe from a list.
     */
    public function removeRecipeFromList(int $recipeId): void
    {
        $list = $this->viewingList();
        $user = auth()->user();

        if (! $list instanceof RecipeList || ! $user) {
            return;
        }

        if (! $list->isOwnedBy($user)) {
            return;
        }

        $list->recipes()->detach($recipeId);

        unset($this->viewingList, $this->recipeLists, $this->recipesByCountry);

        Flux::toastSuccess(__('Recipe removed from list.'));
    }

    public function render(): View
    {
        return view('portal::livewire.recipe-lists')
            ->title(__('My Recipe Lists'));
    }
}
