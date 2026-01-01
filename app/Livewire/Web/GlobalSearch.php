<?php

namespace App\Livewire\Web;

use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use App\Models\Recipe;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Attributes\Computed;

class GlobalSearch extends AbstractComponent
{
    use WithLocalizedContextTrait;

    public string $search = '';

    /**
     * Get the matching recipes with their menus.
     *
     * @return Collection<int, Recipe>
     */
    #[Computed]
    public function recipes(): Collection
    {
        if (blank($this->search)) {
            return new Collection();
        }

        $searchTerm = sprintf('%%%s%%', $this->search);

        return Recipe::with(['menus' => function (Relation $query): void {
            $query->orderByDesc('year_week')->limit(3);
        }])
            ->where('country_id', $this->countryId)
            ->where(function (Builder $query) use ($searchTerm): void {
                $query->whereLike('name->' . $this->locale, $searchTerm)
                    ->orWhereLike('headline->' . $this->locale, $searchTerm);
            })
            ->orderBy('name->' . $this->locale)
            ->limit(10)
            ->get();
    }

    /**
     * Navigate to a recipe.
     */
    public function selectRecipe(int $recipeId): void
    {
        $recipe = Recipe::findOrFail($recipeId);

        $this->redirect(
            localized_route('localized.recipes.show', [
                'recipe' => $recipe->id,
                'slug' => slugify($recipe->name),
            ]),
            navigate: true
        );
    }

    public function render(): ViewInterface
    {
        return view('web::livewire.global-search');
    }
}
