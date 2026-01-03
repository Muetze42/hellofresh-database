<?php

namespace App\Livewire\Web\Recipes;

use App\Enums\IngredientMatchModeEnum;
use App\Enums\RecipeSortEnum;
use App\Enums\ViewModeEnum;
use App\Livewire\AbstractComponent;
use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use App\Livewire\Web\Recipes\Concerns\WithRecipeFilterOptionsTrait;
use App\Models\Menu;
use App\Models\Recipe;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[Layout('web::components.layouts.localized')]
class RecipeIndex extends AbstractComponent
{
    use WithLocalizedContextTrait;
    use WithPagination;
    use WithRecipeFilterOptionsTrait;

    protected int $perPage = 12;

    public ?Menu $menu = null;

    public ?int $selectedMenuWeek = null;

    #[Url]
    public string $search = '';

    public string $viewMode = '';

    public string $sortBy = '';

    public bool $filterHasPdf = false;

    /** @var array<int> */
    public array $excludedAllergenIds = [];

    /** @var array<int> */
    public array $ingredientIds = [];

    public string $ingredientMatchMode = IngredientMatchModeEnum::Any->value;

    public string $ingredientSearch = '';

    /** @var array<int> */
    public array $excludedIngredientIds = [];

    public string $excludedIngredientSearch = '';

    /** @var array<int> */
    public array $tagIds = [];

    /** @var array<int> */
    public array $excludedTagIds = [];

    /** @var array<int> */
    public array $labelIds = [];

    /** @var array<int> */
    public array $excludedLabelIds = [];

    /** @var array<int> */
    public array $difficultyLevels = [];

    /** @var array{0: int, 1: int}|null */
    public ?array $prepTimeRange = null;

    /** @var array{0: int, 1: int}|null */
    public ?array $totalTimeRange = null;

    /**
     * Initialize the component.
     */
    public function mount(?Menu $menu = null): void
    {
        $this->menu = $this->resolveMenu($menu);
        $this->selectedMenuWeek = $this->menu?->year_week;
        $this->viewMode = session('view_mode', ViewModeEnum::Grid->value);
        $this->sortBy = session($this->filterSessionKey('sort'), RecipeSortEnum::NewestFirst->value);
        $this->filterHasPdf = session($this->filterSessionKey('has_pdf'), false);
        $this->excludedAllergenIds = session($this->filterSessionKey('excluded_allergens'), []);
        $this->ingredientIds = session($this->filterSessionKey('ingredients'), []);
        $this->ingredientMatchMode = session($this->filterSessionKey('ingredient_match'), IngredientMatchModeEnum::Any->value);
        $this->excludedIngredientIds = session($this->filterSessionKey('excluded_ingredients'), []);
        $this->tagIds = session($this->filterSessionKey('tags'), []);
        $this->excludedTagIds = session($this->filterSessionKey('excluded_tags'), []);
        $this->labelIds = session($this->filterSessionKey('labels'), []);
        $this->excludedLabelIds = session($this->filterSessionKey('excluded_labels'), []);
        $this->difficultyLevels = session($this->filterSessionKey('difficulty'), []);
        $this->prepTimeRange = session($this->filterSessionKey('prep_time'), $this->getDefaultPrepTimeRange());
        $this->totalTimeRange = session($this->filterSessionKey('total_time'), $this->getDefaultTotalTimeRange());
    }

    /**
     * Resolve the menu for the current country.
     */
    protected function resolveMenu(?Menu $menu): ?Menu
    {
        return match (true) {
            ! $menu instanceof Menu => null,
            $menu->country_id === $this->countryId => $menu,
            default => Menu::where('country_id', $this->countryId)
                ->where('year_week', $menu->year_week)
                ->first(),
        };
    }

    /**
     * Get the default prep time range (matching slider min/max).
     *
     * @return array{0: int, 1: int}|null
     */
    protected function getDefaultPrepTimeRange(): ?array
    {
        $country = $this->country();

        if ($country->prep_min === null || $country->prep_max === null) {
            return null;
        }

        return [0, (int) ceil($country->prep_max / 10) * 10];
    }

    /**
     * Get the default total time range (matching slider min/max).
     *
     * @return array{0: int, 1: int}|null
     */
    protected function getDefaultTotalTimeRange(): ?array
    {
        $country = $this->country();

        if ($country->total_min === null || $country->total_max === null) {
            return null;
        }

        return [0, (int) ceil($country->total_max / 10) * 10];
    }

    /**
     * Check if prep time filter is active (different from full range).
     */
    public function isPrepTimeFilterActive(): bool
    {
        if ($this->prepTimeRange === null) {
            return false;
        }

        $default = $this->getDefaultPrepTimeRange();

        if ($default === null) {
            return false;
        }

        return (int) $this->prepTimeRange[0] !== $default[0] || (int) $this->prepTimeRange[1] !== $default[1];
    }

    /**
     * Check if total time filter is active (different from full range).
     */
    public function isTotalTimeFilterActive(): bool
    {
        if ($this->totalTimeRange === null) {
            return false;
        }

        $default = $this->getDefaultTotalTimeRange();

        if ($default === null) {
            return false;
        }

        return (int) $this->totalTimeRange[0] !== $default[0] || (int) $this->totalTimeRange[1] !== $default[1];
    }

    /**
     * Get a country-specific session key for filters.
     */
    protected function filterSessionKey(string $key): string
    {
        return sprintf('recipe_filter_%d_%s', $this->countryId, $key);
    }

    /**
     * Get the current sort enum.
     */
    protected function getSortEnum(): RecipeSortEnum
    {
        return RecipeSortEnum::tryFrom($this->sortBy) ?? RecipeSortEnum::NewestFirst;
    }

    /**
     * Handle property updates and persist to session.
     */
    public function updated(string $property): void
    {
        // Extract base property name (e.g., 'ingredientIds.0' -> 'ingredientIds')
        $baseProperty = explode('.', $property)[0];
        $sessionMapping = $this->getSessionMapping();

        if ($baseProperty === 'viewMode') {
            session(['view_mode' => $this->viewMode]);

            return;
        }

        if ($baseProperty === 'selectedMenuWeek' && $this->selectedMenuWeek !== null) {
            $this->redirect(localized_route('localized.menus.show', ['menu' => $this->selectedMenuWeek]));

            return;
        }

        if ($baseProperty === 'search') {
            $this->resetPage();

            return;
        }

        if (! isset($sessionMapping[$baseProperty])) {
            return;
        }

        session([$this->filterSessionKey($sessionMapping[$baseProperty]) => $this->{$baseProperty}]);

        // Don't reset page for ingredient match mode if no ingredients selected
        if ($baseProperty === 'ingredientMatchMode' && $this->ingredientIds === []) {
            return;
        }

        $this->resetPage();
    }

    /**
     * Get mapping of property names to session keys.
     *
     * @return array<string, string>
     */
    protected function getSessionMapping(): array
    {
        return [
            'sortBy' => 'sort',
            'filterHasPdf' => 'has_pdf',
            'excludedAllergenIds' => 'excluded_allergens',
            'ingredientIds' => 'ingredients',
            'ingredientMatchMode' => 'ingredient_match',
            'excludedIngredientIds' => 'excluded_ingredients',
            'tagIds' => 'tags',
            'excludedTagIds' => 'excluded_tags',
            'labelIds' => 'labels',
            'excludedLabelIds' => 'excluded_labels',
            'difficultyLevels' => 'difficulty',
            'prepTimeRange' => 'prep_time',
            'totalTimeRange' => 'total_time',
        ];
    }

    /**
     * Get the count of active filters.
     */
    #[Computed]
    public function activeFilterCount(): int
    {
        $count = 0;

        if ($this->filterHasPdf) {
            $count++;
        }

        if ($this->excludedAllergenIds !== []) {
            $count++;
        }

        if ($this->ingredientIds !== []) {
            $count++;
        }

        if ($this->excludedIngredientIds !== []) {
            $count++;
        }

        if ($this->tagIds !== []) {
            $count++;
        }

        if ($this->excludedTagIds !== []) {
            $count++;
        }

        if ($this->labelIds !== []) {
            $count++;
        }

        if ($this->excludedLabelIds !== []) {
            $count++;
        }

        if ($this->difficultyLevels !== []) {
            $count++;
        }

        if ($this->isPrepTimeFilterActive()) {
            $count++;
        }

        if ($this->isTotalTimeFilterActive()) {
            $count++;
        }

        return $count;
    }

    /**
     * Toggle a tag filter (add or remove).
     */
    public function toggleTag(int $id): void
    {
        if (in_array($id, $this->tagIds, true)) {
            $this->tagIds = array_values(array_filter($this->tagIds, fn (int $tagId): bool => $tagId !== $id));
            session([$this->filterSessionKey('tags') => $this->tagIds]);
            $this->resetPage();

            return;
        }

        $this->tagIds[] = $id;
        session([$this->filterSessionKey('tags') => $this->tagIds]);
        $this->resetPage();
    }

    /**
     * Check if a tag filter is active.
     */
    public function isTagActive(int $tagId): bool
    {
        return in_array($tagId, $this->tagIds, true);
    }

    /**
     * Clear all filters.
     */
    public function clearFilters(): void
    {
        $this->filterHasPdf = false;
        $this->excludedAllergenIds = [];
        $this->ingredientIds = [];
        $this->ingredientMatchMode = IngredientMatchModeEnum::Any->value;
        $this->excludedIngredientIds = [];
        $this->tagIds = [];
        $this->excludedTagIds = [];
        $this->labelIds = [];
        $this->excludedLabelIds = [];
        $this->difficultyLevels = [];
        $this->prepTimeRange = $this->getDefaultPrepTimeRange();
        $this->totalTimeRange = $this->getDefaultTotalTimeRange();

        session()->forget([
            $this->filterSessionKey('has_pdf'),
            $this->filterSessionKey('excluded_allergens'),
            $this->filterSessionKey('ingredients'),
            $this->filterSessionKey('ingredient_match'),
            $this->filterSessionKey('excluded_ingredients'),
            $this->filterSessionKey('tags'),
            $this->filterSessionKey('excluded_tags'),
            $this->filterSessionKey('labels'),
            $this->filterSessionKey('excluded_labels'),
            $this->filterSessionKey('difficulty'),
            $this->filterSessionKey('prep_time'),
            $this->filterSessionKey('total_time'),
        ]);
        $this->resetPage();
    }

    /**
     * Get the paginated recipes.
     *
     * @return LengthAwarePaginator<int, Recipe>
     */
    #[Computed]
    public function recipes(): LengthAwarePaginator
    {
        $menuRecipeIds = $this->menu?->recipes->pluck('id')->all() ?? [];

        return Recipe::where('country_id', $this->countryId)
            ->when($menuRecipeIds !== [], fn (Builder $query) => $query->whereIn('id', $menuRecipeIds))
            ->when($this->search !== '', fn (Builder $query): Builder => $this->applySearchFilter($query))
            ->when($this->filterHasPdf, fn (Builder $query) => $query->where('has_pdf', true))
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
            ->orderBy($this->getSortEnum()->column(), $this->getSortEnum()->direction())
            ->orderBy('id')
            ->paginate($this->perPage);
    }

    /**
     * Get menu data for the current menu and available menus.
     *
     * @return array{current: int, list: list<array{value: int, year: int, start: string, end: string}>}|null
     */
    #[Computed]
    public function menuData(): ?array
    {
        if (! $this->menu instanceof Menu) {
            return null;
        }

        /** @var list<array{value: int, year: int, start: string, end: string}> $list */
        $list = Menu::selectable()
            ->where('country_id', $this->countryId)
            ->orderBy('year_week')
            ->get()
            ->map(fn (Menu $menu): array => [
                'value' => $menu->year_week,
                'year' => (int) substr((string) $menu->year_week, 0, 4),
                'start' => $menu->start->startOfWeek(CarbonInterface::SATURDAY)->translatedFormat('j. M'),
                'end' => $menu->start->endOfWeek(CarbonInterface::FRIDAY)->translatedFormat('j. M'),
            ])->values()->all();

        $currentInList = collect($list)->contains('value', $this->menu->year_week);

        if (! $currentInList) {
            array_unshift($list, [
                'value' => $this->menu->year_week,
                'year' => (int) substr((string) $this->menu->year_week, 0, 4),
                'start' => $this->menu->start->startOfWeek(CarbonInterface::SATURDAY)->translatedFormat('j. M'),
                'end' => $this->menu->start->endOfWeek(CarbonInterface::FRIDAY)->translatedFormat('j. M'),
            ]);
        }

        return [
            'current' => $this->menu->year_week,
            'list' => $list,
        ];
    }

    /**
     * Apply prep time filter.
     *
     * @param  Builder<Recipe>  $query
     * @return Builder<Recipe>
     */
    protected function applyPrepTimeFilter(Builder $query): Builder
    {
        if ($this->prepTimeRange === null) {
            return $query;
        }

        return $query
            ->where('prep_time', '>=', $this->prepTimeRange[0])
            ->where('prep_time', '<=', $this->prepTimeRange[1]);
    }

    /**
     * Apply total time filter.
     *
     * @param  Builder<Recipe>  $query
     * @return Builder<Recipe>
     */
    protected function applyTotalTimeFilter(Builder $query): Builder
    {
        if ($this->totalTimeRange === null) {
            return $query;
        }

        return $query
            ->where('total_time', '>=', $this->totalTimeRange[0])
            ->where('total_time', '<=', $this->totalTimeRange[1]);
    }

    /**
     * Apply search filter across name, headline, tags, ingredients, and labels.
     *
     * @param  Builder<Recipe>  $query
     * @return Builder<Recipe>
     */
    protected function applySearchFilter(Builder $query): Builder
    {
        $searchTerm = sprintf('%%%s%%', $this->search);
        $locale = $this->locale;

        return $query->where(function (Builder $query) use ($searchTerm, $locale): void {
            $query->whereLike('name->' . $locale, $searchTerm)
                ->orWhereLike('headline->' . $locale, $searchTerm)
                ->orWhereLike('hellofresh_id', trim($this->search))
                ->orWhereHas('tags', fn (Builder $tagQuery) => $tagQuery->whereLike('name', $searchTerm))
                ->orWhereHas('ingredients', fn (Builder $ingredientQuery) => $ingredientQuery->whereLike('name', $searchTerm))
                ->orWhereHas('label', fn (Builder $labelQuery) => $labelQuery->whereLike('name', $searchTerm));
        });
    }

    /**
     * Apply ingredient filter based on match mode.
     *
     * @param  Builder<Recipe>  $query
     * @return Builder<Recipe>
     */
    protected function applyIngredientFilter(Builder $query): Builder
    {
        return $query->when(
            $this->ingredientMatchMode === IngredientMatchModeEnum::All->value,
            function (Builder $query): Builder {
                foreach ($this->ingredientIds as $ingredientId) {
                    $query->whereHas(
                        'ingredients',
                        fn (Builder $ingredientQuery) => $ingredientQuery->where('ingredients.id', $ingredientId)
                    );
                }

                return $query;
            },
            fn (Builder $query) => $query->whereHas(
                'ingredients',
                fn (Builder $ingredientQuery) => $ingredientQuery->whereIn('ingredients.id', $this->ingredientIds)
            )
        );
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        if ($this->menu instanceof Menu) {
            $title = page_title(__('Menu') . ' ' . $this->menu->start->translatedFormat('j. M Y'));
            $year = intdiv($this->menu->year_week, 100);
            $week = $this->menu->year_week % 100;

            return view('web::livewire.recipes.recipe-index')
                ->title($title)
                ->layoutData([
                    'ogTitle' => sprintf(__('Menu Week %d/%d'), $week, $year),
                    'ogDescription' => __('Discover all recipes from HelloFresh menu week :week/:year.', ['week' => $week, 'year' => $year]),
                    'ogImage' => route('og.menu', ['menu' => $this->menu, 'locale' => app()->getLocale()]),
                ]);
        }

        $title = page_title(__('Recipes'));

        return view('web::livewire.recipes.recipe-index')
            ->title($title)
            ->layoutData([
                'ogTitle' => __('Recipes'),
                'ogDescription' => __('Browse thousands of HelloFresh recipes. Filter by difficulty, cooking time, allergens and more.'),
            ]);
    }
}
