<?php

namespace App\Jobs\Country;

use App\Contracts\LauncherJobInterface;
use App\Enums\QueueEnum;
use App\Jobs\Country\UpdateRecipeCount\UpdateAllergenRecipeCountJob;
use App\Jobs\Country\UpdateRecipeCount\UpdateCuisineRecipeCountJob;
use App\Jobs\Country\UpdateRecipeCount\UpdateIngredientRecipeCountJob;
use App\Jobs\Country\UpdateRecipeCount\UpdateLabelRecipeCountJob;
use App\Jobs\Country\UpdateRecipeCount\UpdateTagRecipeCountJob;
use App\Jobs\Country\UpdateRecipeCount\UpdateUtensilRecipeCountJob;
use App\Models\Country;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Throwable;

/**
 * Dispatches jobs to update recipe counts for all country resources.
 */
class UpdateCountryResourcesRecipeCountJob implements LauncherJobInterface, ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Country $country,
    ) {
        $this->onQueue(QueueEnum::Default->value);
    }

    public static function description(): string
    {
        return 'Update recipe counts for a country\'s resources';
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        Bus::batch([
            new UpdateIngredientRecipeCountJob($this->country),
            new UpdateAllergenRecipeCountJob($this->country),
            new UpdateTagRecipeCountJob($this->country),
            new UpdateLabelRecipeCountJob($this->country),
            new UpdateCuisineRecipeCountJob($this->country),
            new UpdateUtensilRecipeCountJob($this->country),
        ])
            ->name('Update Recipe Counts: ' . $this->country->code)
            ->onQueue(QueueEnum::Long->value)
            ->dispatch();
    }
}
