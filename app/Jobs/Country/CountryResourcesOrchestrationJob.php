<?php

namespace App\Jobs\Country;

use App\Contracts\LauncherJobInterface;
use App\Enums\QueueEnum;
use App\Models\Country;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Orchestrator job that dispatches UpdateActivatableModelsForCountryJob for each country.
 */
class CountryResourcesOrchestrationJob implements LauncherJobInterface, ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onQueue(QueueEnum::Long->value);
    }

    public static function description(): string
    {
        return 'Update active status for activatable models for all countries';
    }

    public function handle(): void
    {
        Country::each(static fn (Country $country) => ActivateCountryResourcesJob::dispatch($country));
    }
}
