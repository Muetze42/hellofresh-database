<?php

namespace App\Jobs\Country;

use App\Enums\QueueEnum;
use App\Jobs\Country\ActivateCountryResources\ActivateAllergensJob;
use App\Jobs\Country\ActivateCountryResources\ActivateCuisinesJob;
use App\Jobs\Country\ActivateCountryResources\ActivateIngredientsJob;
use App\Jobs\Country\ActivateCountryResources\ActivateLabelsJob;
use App\Jobs\Country\ActivateCountryResources\ActivateTagsJob;
use App\Jobs\Country\ActivateCountryResources\ActivateUtensilsJob;
use App\Models\Country;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Throwable;

/**
 * Dispatches jobs to update the active status for activatable models based on recipe count.
 */
class ActivateCountryResourcesJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Country $country,
    ) {
        $this->onQueue(QueueEnum::Default->value);
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        Bus::batch([
            new ActivateAllergensJob($this->country),
            new ActivateCuisinesJob($this->country),
            new ActivateTagsJob($this->country),
            new ActivateUtensilsJob($this->country),
            new ActivateLabelsJob($this->country),
            new ActivateIngredientsJob($this->country),
        ])
            ->name('Activate Country Resources: ' . $this->country->code)
            ->onQueue(QueueEnum::Long->value)
            ->dispatch();
    }
}
