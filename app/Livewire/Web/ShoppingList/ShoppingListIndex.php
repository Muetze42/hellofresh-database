<?php

namespace App\Livewire\Web\ShoppingList;

use App\Livewire\Web\AbstractComponent;
use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\ShoppingList;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('web::components.layouts.localized')]
class ShoppingListIndex extends AbstractComponent
{
    use WithLocalizedContextTrait;

    /** @var array<int> */
    public array $recipeIds = [];

    /** @var array<int, int> */
    public array $servings = [];

    public string $saveListName = '';

    #[Url(as: 'mode')]
    public string $printMode = '';

    #[Url(as: 'style')]
    public string $printStyle = 'combined';

    /**
     * Load recipes by IDs from Alpine/localStorage.
     *
     * @param  array<int>  $ids
     * @param  array<int, int>  $servingsData
     */
    public function loadRecipes(array $ids, array $servingsData = []): void
    {
        $this->recipeIds = array_map(intval(...), $ids);
        $this->servings = array_map(intval(...), $servingsData);
    }

    /**
     * Get the recipes for the shopping list.
     *
     * @return Collection<int, Recipe>
     */
    #[Computed]
    public function recipes(): Collection
    {
        if ($this->recipeIds === []) {
            return new Collection();
        }

        $recipeIds = $this->recipeIds;

        return Recipe::whereIn('id', $this->recipeIds)
            ->where('country_id', $this->countryId)
            ->with('ingredients')
            ->get()
            ->sortBy(static fn (Recipe $recipe): int|string|false => array_search($recipe->id, $recipeIds, true));
    }

    /**
     * Get available yields for a recipe.
     *
     * @return list<int>
     */
    public function getYieldsForRecipe(Recipe $recipe): array
    {
        /** @var list<array{yields: int, ingredients: array<int, mixed>}> $yields */
        $yields = $recipe->yields_primary ?? [];

        return array_column($yields, 'yields');
    }

    /**
     * Get the default yield (first available) for a recipe.
     */
    public function getDefaultYield(Recipe $recipe): int
    {
        $yields = $this->getYieldsForRecipe($recipe);

        return $yields[0] ?? 2;
    }

    /**
     * Get the current servings for a recipe.
     */
    public function getServingsForRecipe(Recipe $recipe): int
    {
        return $this->servings[$recipe->id] ?? $this->getDefaultYield($recipe);
    }

    /**
     * Get aggregated ingredients across all recipes.
     *
     * @return array<int, array{ingredient: Ingredient, items: list<array{recipe: Recipe, amount: float|null, unit: string, servings: int}>, total: float, unit: string}>
     */
    #[Computed]
    public function aggregatedIngredients(): array
    {
        $aggregated = [];

        foreach ($this->recipes() as $recipe) {
            $servings = $this->servings[$recipe->id] ?? $this->getDefaultYield($recipe);
            $yieldsData = $this->getYieldsDataForServings($recipe, $servings);

            foreach ($recipe->ingredients as $ingredient) {
                $ingredientData = $this->findIngredientInYieldsForIngredient($yieldsData, $ingredient);

                $key = $ingredient->id;

                if (! isset($aggregated[$key])) {
                    $aggregated[$key] = [
                        'ingredient' => $ingredient,
                        'items' => [],
                        'total' => 0.0,
                        'unit' => $ingredientData['unit'],
                    ];
                }

                $amount = $ingredientData['amount'];

                $aggregated[$key]['items'][] = [
                    'recipe' => $recipe,
                    'amount' => $amount,
                    'unit' => $ingredientData['unit'],
                    'servings' => $servings,
                ];

                if ($amount !== null) {
                    $aggregated[$key]['total'] += $amount;
                }
            }
        }

        // Sort by ingredient name
        uasort($aggregated, static fn (array $itemA, array $itemB): int => strcasecmp((string) $itemA['ingredient']->name, (string) $itemB['ingredient']->name));

        return $aggregated;
    }

    /**
     * Get yields data for specific servings count.
     *
     * @return array{yields?: int, ingredients?: list<array{id: string, amount: float|null, unit: string}>}
     */
    protected function getYieldsDataForServings(Recipe $recipe, int $servings): array
    {
        /** @var list<array{yields: int, ingredients: list<array{id: string, amount: float|null, unit: string}>}> $yields */
        $yields = $recipe->yields_primary ?? [];

        foreach ($yields as $yield) {
            if ($yield['yields'] === $servings) {
                return $yield;
            }
        }

        return $yields[0] ?? [];
    }

    /**
     * Find ingredient data in yields for a given ingredient model.
     *
     * @param  array{yields?: int, ingredients?: list<array{id: string, amount: float|null, unit: string}>}  $yieldsData
     * @return array{amount: float|null, unit: string}
     */
    protected function findIngredientInYieldsForIngredient(array $yieldsData, Ingredient $ingredient): array
    {
        $yieldIngredients = $yieldsData['ingredients'] ?? [];

        /** @var list<string> $hellofreshIds */
        $hellofreshIds = $ingredient->hellofresh_ids ?? [];

        foreach ($yieldIngredients as $yieldIngredient) {
            if (in_array($yieldIngredient['id'], $hellofreshIds, true)) {
                return [
                    'amount' => $yieldIngredient['amount'],
                    'unit' => $yieldIngredient['unit'],
                ];
            }
        }

        return ['amount' => null, 'unit' => ''];
    }

    /**
     * Save the current shopping list.
     */
    public function saveList(): void
    {
        $this->validate([
            'saveListName' => ['required', 'string', 'min:2', 'max:255'],
        ]);

        $user = auth()->user();

        if (! $user) {
            $this->dispatch('require-auth');

            return;
        }

        $items = [];
        foreach ($this->recipeIds as $recipeId) {
            $items[] = [
                'recipe_id' => $recipeId,
                'servings' => $this->servings[$recipeId] ?? 2,
            ];
        }

        $list = new ShoppingList([
            'name' => $this->saveListName,
            'items' => $items,
        ]);

        $list->user()->associate($user);
        $list->country()->associate($this->countryId);
        $list->save();

        $this->reset('saveListName');

        Flux::closeModal('save-shopping-list');
        Flux::toastSuccess(__('Shopping list saved!'));
    }

    /**
     * Open save modal.
     */
    public function openSaveModal(): void
    {
        $user = auth()->user();

        if (! $user) {
            $this->dispatch('require-auth');

            return;
        }

        Flux::showModal('save-shopping-list');
    }

    /**
     * Check if we're in print mode.
     */
    public function isPrintMode(): bool
    {
        return $this->printMode === 'print';
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        return view('web::livewire.shopping-list.shopping-list-index')
            ->title(page_title(__('Shopping List')))
            ->layoutData([
                'ogDescription' => __('Create your shopping list from HelloFresh recipes.'),
            ]);
    }
}
