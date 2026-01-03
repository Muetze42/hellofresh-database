<?php

namespace App\Livewire\Portal;

use App\Models\Allergen;
use App\Models\Country;
use App\Models\Cuisine;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use stdClass;

#[Layout('portal::components.layouts.app')]
class Statistic extends Component
{
    public string $sortBy = 'recipes_count';

    public string $sortDirection = 'desc';

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
        return Cache::remember('portal_global_stats', 3600, fn (): array => [
            'recipes' => Recipe::count(),
            'ingredients' => Ingredient::count(),
            'menus' => Menu::count(),
            'countries' => Country::where('active', true)->count(),
            'tags' => Tag::count(),
            'allergens' => Allergen::count(),
            'cuisines' => Cuisine::count(),
        ]);
    }

    /**
     * Get per-country statistics.
     *
     * @return Collection<int, Country>
     */
    #[Computed]
    public function countryStats(): Collection
    {
        $countries = Cache::remember('portal_country_stats', 3600, fn (): Collection => Country::where('active', true)
            ->withCount('menus')
            ->get());

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
        return Cache::remember('portal_newest_recipes', 3600, fn (): Collection => Recipe::with('country')
            ->latest('created_at')
            ->limit(5)
            ->get());
    }

    /**
     * Get recipe count per difficulty level.
     *
     * @return array<int, array{difficulty: int, count: int}>
     */
    #[Computed]
    public function difficultyDistribution(): array
    {
        return Cache::remember('portal_difficulty_distribution', 3600, function (): array {
            $results = DB::table('recipes')
                ->selectRaw('difficulty, COUNT(*) as count')
                ->whereNotNull('difficulty')
                ->groupBy('difficulty')
                ->orderBy('difficulty')
                ->get();

            return $results->map(fn (stdClass $row): array => [
                'difficulty' => (int) $row->difficulty,
                'count' => (int) $row->count,
            ])->all();
        });
    }

    public function render(): View
    {
        return view('portal::livewire.statistic')->title('Database Statistics');
    }
}
