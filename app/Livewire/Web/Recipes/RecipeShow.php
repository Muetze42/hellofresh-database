<?php

namespace App\Livewire\Web\Recipes;

use App\Livewire\AbstractComponent;
use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('web::components.layouts.localized')]
class RecipeShow extends AbstractComponent
{
    use WithLocalizedContextTrait;

    public Recipe $recipe;

    public int $selectedYield = 2;

    /**
     * Initialize the component.
     */
    public function mount(Recipe $recipe): void
    {
        // Abort if recipe belongs to a different country than the URL context
        abort_if($recipe->country_id !== $this->countryId, 404);

        $this->recipe = $recipe->load([
            'country',
            'label',
            'tags',
            'allergens',
            'cuisines',
            'utensils',
            'ingredients',
            'canonical.country',
            'variants.country',
            'variants.label',
            'variants.tags',
        ]);

        // Set default yield based on available yields
        $yields = $this->getAvailableYieldsArray();
        if ($yields !== []) {
            $this->selectedYield = $yields[0];
        }
    }

    /**
     * Toggle a tag filter and redirect to recipe index.
     */
    public function toggleTagFilter(int $tagId): void
    {
        $sessionKey = sprintf('recipe_filter_%d_tags', $this->recipe->country_id);
        /** @var array<int> $tagIds */
        $tagIds = session($sessionKey, []);

        if (in_array($tagId, $tagIds, true)) {
            $tagIds = array_values(array_filter($tagIds, fn (int $id): bool => $id !== $tagId));
            session([$sessionKey => $tagIds]);
            $this->redirect(localized_route('localized.recipes.index'));

            return;
        }

        $tagIds[] = $tagId;
        session([$sessionKey => $tagIds]);
        $this->redirect(localized_route('localized.recipes.index'));
    }

    /**
     * Check if a tag is currently active in the filter.
     */
    public function isTagActive(int $tagId): bool
    {
        $sessionKey = sprintf('recipe_filter_%d_tags', $this->recipe->country_id);
        /** @var array<int> $tagIds */
        $tagIds = session($sessionKey, []);

        return in_array($tagId, $tagIds, true);
    }

    /**
     * Get yields array from recipe.
     *
     * @return list<array<string, mixed>>
     */
    protected function getYieldsArray(): array
    {
        /** @var array<int|string, array<string, mixed>>|null $yields */
        $yields = $this->recipe->yields_primary;

        return $yields !== null ? array_values($yields) : [];
    }

    /**
     * Get available yield values.
     *
     * @return list<int>
     */
    protected function getAvailableYieldsArray(): array
    {
        return array_map(
            fn (array $yield): int => (int) $yield['yields'],
            $this->getYieldsArray()
        );
    }

    /**
     * Get available yield options.
     *
     * @return list<int>
     */
    #[Computed]
    public function availableYields(): array
    {
        return $this->getAvailableYieldsArray();
    }

    /**
     * Get ingredients for the selected yield.
     *
     * @return list<array{ingredient: Ingredient|null, amount: float|null, unit: string}>
     */
    #[Computed(persist: false, cache: false)]
    public function ingredientsForYield(): array
    {
        $yields = $this->getYieldsArray();
        $selectedYieldData = array_find($yields, fn (array $yield): bool => (int) $yield['yields'] === $this->selectedYield);

        if ($selectedYieldData === null) {
            return [];
        }

        // Build a map of hellofresh_id => Ingredient for lookup
        // The hellofresh_ids column is a jsonb array, so we need to map each ID to its ingredient
        $ingredientMap = collect();
        foreach ($this->recipe->ingredients as $ingredient) {
            /** @var list<string>|null $hellofreshIds */
            $hellofreshIds = $ingredient->hellofresh_ids;
            if ($hellofreshIds !== null) {
                foreach ($hellofreshIds as $hellofreshId) {
                    $ingredientMap->put($hellofreshId, $ingredient);
                }
            }
        }

        $ingredients = $selectedYieldData['ingredients'] ?? [];

        if (! is_array($ingredients)) {
            return [];
        }

        return array_values(array_map(function (array $item) use ($ingredientMap): array {
            return [
                'ingredient' => $ingredientMap->get($item['id']),
                'amount' => $item['amount'] ?? null,
                'unit' => $item['unit'] ?? '',
            ];
        }, $ingredients));
    }

    /**
     * Get preparation steps.
     *
     * @return list<array<string, mixed>>
     */
    #[Computed]
    public function steps(): array
    {
        /** @var array<int|string, array<string, mixed>>|null $steps */
        $steps = $this->recipe->steps_primary;

        if ($steps === null) {
            return [];
        }

        return array_values(array_map(function (array $step): array {
            if (isset($step['instructions']) && is_string($step['instructions'])) {
                // Remove inline styles that break dark mode
                $step['instructions'] = preg_replace('/\s*style="[^"]*"/i', '', $step['instructions']);
            }

            return $step;
        }, $steps));
    }

    /**
     * Get nutrition information.
     *
     * @return list<array<string, mixed>>
     */
    #[Computed]
    public function nutrition(): array
    {
        /** @var array<int|string, array<string, mixed>>|null $nutrition */
        $nutrition = $this->recipe->nutrition_primary;

        return $nutrition !== null ? array_values($nutrition) : [];
    }

    /**
     * Get similar recipes based on shared tags.
     *
     * @return Collection<int, Recipe>
     */
    #[Computed]
    public function similarRecipes(): Collection
    {
        $tagIds = $this->recipe->tags->pluck('id')->all();

        if ($tagIds === []) {
            return new Collection();
        }

        $sessionKey = sprintf('recipe_filter_%d_excluded_allergens', $this->recipe->country_id);
        /** @var array<int> $excludedAllergenIds */
        $excludedAllergenIds = session($sessionKey, []);

        return Recipe::where('country_id', $this->recipe->country_id)
            ->whereNot('id', $this->recipe->id)
            ->whereNotNull('name->' . app()->getLocale())
            ->whereHas('tags', fn (Builder $query): Builder => $query->whereIn('tags.id', $tagIds))
            ->when($excludedAllergenIds !== [], fn (Builder $query) => $query->whereDoesntHave(
                'allergens',
                fn (Builder $allergenQuery) => $allergenQuery->whereIn('allergens.id', $excludedAllergenIds)
            ))
            ->withCount(['tags' => fn (Builder $query): Builder => $query->whereIn('tags.id', $tagIds)])
            ->orderByDesc('tags_count')
            ->with(['label', 'tags', 'country'])
            ->limit(4)
            ->get();
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        $title = page_title($this->recipe->name ?: $this->recipe->getFirstTranslation('name'));

        $description = $this->recipe->description
            ?: ($this->recipe->headline
                ? $this->recipe->name . ' ' . $this->recipe->headline
                : $this->recipe->name);

        return view('web::livewire.recipes.recipe-show')
            ->title($title)
            ->layoutData([
                'ogTitle' => $this->recipe->name,
                'ogDescription' => $description,
                'ogImage' => route('og.recipe', $this->recipe),
            ]);
    }
}
