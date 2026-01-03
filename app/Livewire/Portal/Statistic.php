<?php

namespace App\Livewire\Portal;

use App\Models\Country;
use App\Models\Recipe;
use App\Services\Portal\StatisticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
class Statistic extends Component
{
    public string $sortBy = 'recipes_count';

    public string $sortDirection = 'desc';

    public function __construct(
        protected StatisticsService $statistics,
    ) {}

    /**
     * Sort the country stats by a given column.
     */
    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortBy = $column;
        $this->sortDirection = 'asc';
    }

    /**
     * Get global statistics.
     *
     * @return array{recipes: int, ingredients: int, menus: int, countries: int, tags: int, allergens: int, cuisines: int}
     */
    #[Computed]
    public function globalStats(): array
    {
        return $this->statistics->globalStats();
    }

    /**
     * Get per-country statistics.
     *
     * @return Collection<int, Country>
     */
    #[Computed]
    public function countryStats(): Collection
    {
        $countries = $this->statistics->countryStats();

        return $this->sortDirection === 'asc'
            ? $countries->sortBy($this->sortBy)
            : $countries->sortByDesc($this->sortBy);
    }

    /**
     * Get the newest recipes.
     *
     * @return Collection<int, Recipe>
     */
    #[Computed]
    public function newestRecipes(): Collection
    {
        return $this->statistics->newestRecipes();
    }

    /**
     * Get recipe count per difficulty level.
     *
     * @return array<int, array{difficulty: int, count: int}>
     */
    #[Computed]
    public function difficultyDistribution(): array
    {
        return $this->statistics->difficultyDistribution();
    }

    /**
     * Get recipe quality statistics.
     *
     * @return array{total: int, without_image: int, without_nutrition: int, with_pdf: int, pdf_percentage: float}
     */
    #[Computed]
    public function recipeQuality(): array
    {
        return $this->statistics->recipeQuality();
    }

    /**
     * Get top ingredients by recipe count.
     *
     * @return Collection<int, object{name: string, country_code: string, recipes_count: int}>
     */
    #[Computed]
    public function topIngredients(): Collection
    {
        return $this->statistics->topIngredients();
    }

    /**
     * Get top tags by recipe count.
     *
     * @return Collection<int, object{name: string, country_code: string, recipes_count: int}>
     */
    #[Computed]
    public function topTags(): Collection
    {
        return $this->statistics->topTags();
    }

    /**
     * Get top cuisines by recipe count.
     *
     * @return Collection<int, object{name: string, country_code: string, recipes_count: int}>
     */
    #[Computed]
    public function topCuisines(): Collection
    {
        return $this->statistics->topCuisines();
    }

    /**
     * Get recipes per month for the last 12 months.
     *
     * @return Collection<int, object{month: string, count: int}>
     */
    #[Computed]
    public function recipesPerMonth(): Collection
    {
        return $this->statistics->recipesPerMonth();
    }

    /**
     * Get user engagement statistics.
     *
     * @return array{total_users: int, users_with_lists: int, total_lists: int, total_recipes_in_lists: int}
     */
    #[Computed]
    public function userEngagement(): array
    {
        return $this->statistics->userEngagement();
    }

    /**
     * Get average prep times per country.
     *
     * @return Collection<int, object{code: string, avg_prep: float, avg_total: float}>
     */
    #[Computed]
    public function avgPrepTimesByCountry(): Collection
    {
        return $this->statistics->avgPrepTimesByCountry();
    }

    /**
     * Get data health statistics.
     *
     * @return array{orphan_ingredients: int, inactive_countries: int, recipes_without_tags: int}
     */
    #[Computed]
    public function dataHealth(): array
    {
        return $this->statistics->dataHealth();
    }

    public function render(): View
    {
        return view('portal::livewire.statistic.index')->title('Database Statistics');
    }
}
