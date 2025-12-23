<?php

namespace App\Jobs;

use App\Contracts\LauncherJobInterface;
use App\Enums\QueueEnum;
use App\Models\Country;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Throwable;

class SyncMenusJob implements LauncherJobInterface, ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue(QueueEnum::HelloFresh->value);
    }

    /**
     * The console command description.
     */
    public static function description(): string
    {
        return 'Sync weekly menus from HelloFresh API for all countries';
    }

    /**
     * Execute the job.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        $jobs = [];
        $countries = Country::orderBy('id')->get();

        foreach ($countries as $country) {
            $jobs[] = new FetchMenusJob($country);
        }

        Bus::batch($jobs)
            ->name('Sync HelloFresh Menus')
            ->onQueue(QueueEnum::HelloFresh->value)
            ->dispatch();
    }
}
