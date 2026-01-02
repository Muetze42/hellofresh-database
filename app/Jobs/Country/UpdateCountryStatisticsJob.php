<?php

namespace App\Jobs\Country;

use App\Contracts\LauncherJobInterface;
use App\Enums\QueueEnum;
use App\Models\Country;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Updates statistics columns for countries (prep times, counts, and boolean flags).
 */
class UpdateCountryStatisticsJob implements LauncherJobInterface, ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected ?Country $country = null)
    {
        $this->onQueue(QueueEnum::Long->value);
    }

    /**
     * The console command description.
     */
    public static function description(): string
    {
        return 'Update country statistics (recipe counts, prep times, active flags)';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->country instanceof Country) {
            $this->updateCountryStatistics($this->country);

            return;
        }

        Country::each(fn (Country $country) => $this->updateCountryStatistics($country));
    }

    /**
     * Update statistics for a single country.
     */
    protected function updateCountryStatistics(Country $country): void
    {
        $recipes = $country->recipes();
        $prepRecipes = (clone $recipes)->where('prep_time', '>', 0);
        $totalRecipes = (clone $recipes)->where('total_time', '>', 0);

        $recipesCount = $recipes->count();
        $ingredientsCount = $country->ingredients()->count();

        $prepMin = $prepRecipes->min('prep_time');
        $prepMax = $prepRecipes->max('prep_time');
        $totalMin = $totalRecipes->min('total_time');
        $totalMax = $totalRecipes->max('total_time');

        $country->update([
            'prep_min' => $prepMin > 0 ? $prepMin : null,
            'prep_max' => $prepMax > 0 ? $prepMax : null,
            'total_min' => $totalMin > 0 ? $totalMin : null,
            'total_max' => $totalMax > 0 ? $totalMax : null,
            'recipes_count' => $recipesCount > 0 ? $recipesCount : null,
            'ingredients_count' => $ingredientsCount > 0 ? $ingredientsCount : null,
            'has_allergens' => $country->allergens()->where('active', true)->count() > 3,
            'has_cuisines' => $country->cuisines()->where('active', true)->count() > 3,
            'has_labels' => $country->labels()->where('active', true)->count() > 3,
            'has_tags' => $country->tags()->where('active', true)->count() > 3,
            'has_utensil' => $country->utensils()->where('active', true)->count() > 3,
        ]);
    }
}
