<?php

namespace App\Jobs;

use App\Contracts\LauncherJobInterface;
use App\Enums\QueueEnum;
use App\Models\Country;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @method static void dispatch(bool $fullSync = false)
 */
class SyncRecipesJob implements LauncherJobInterface, ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param  bool  $fullSync  Whether to paginate through all recipes (true) or only fetch the first page (false)
     */
    public function __construct(
        public bool $fullSync = false,
    ) {
        $this->onQueue(QueueEnum::HelloFresh->value);
    }

    /**
     * The console command description.
     */
    public static function description(): string
    {
        return 'Sync recipes from HelloFresh API (daily: first page only, full: all pages)';
    }

    /**
     * Execute the job.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        Log::debug('Syncing HelloFresh recipes...');
        $countries = Country::orderBy('id')->get();

        $primaryJobs = [];
        $secondaryJobs = [];
        foreach ($countries as $country) {
            $primaryLocale = $country->locales[0] ?? null;
            if ($primaryLocale !== null) {
                $primaryJobs[] = new FetchRecipePageJob($country, $primaryLocale, paginates: $this->fullSync);
            }

            $secondaryLocale = $country->locales[1] ?? null;
            if ($secondaryLocale !== null) {
                $secondaryJobs[] = new FetchRecipePageJob($country, $secondaryLocale, paginates: $this->fullSync);
            }
        }

        Bus::batch([...$primaryJobs, ...$secondaryJobs])
            ->name('Sync HelloFresh Recipes')
            ->onQueue(QueueEnum::HelloFresh->value)
            // ->then(static function (Batch $batch): void {
            //     UpdateCountryStatisticsJob::dispatch();
            // })
            ->dispatch();
    }
}
