<?php

namespace App\Jobs\Country;

use App\Contracts\LauncherJobInterface;
use App\Enums\QueueEnum;
use App\Models\Country;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Orchestrator job that dispatches UpdateCountryResourcesRecipeCountJob for each country.
 */
class UpdateResourcesRecipeCountOrchestrationJob implements LauncherJobInterface, ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onQueue(QueueEnum::Long->value);
    }

    public static function description(): string
    {
        return 'Orchestration: Update recipe counts for all country resources';
    }

    public function handle(): void
    {
        Country::each(static function (Country $country): void {
            UpdateCountryResourcesRecipeCountJob::dispatch($country);
        });
    }
}
