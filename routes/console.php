<?php

use App\Jobs\SyncMenusJob;
use App\Jobs\SyncRecipesJob;
use App\Jobs\UpdateCountryStatisticsJob;
use Illuminate\Console\Scheduling\Schedule as ConsoleSchedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SyncRecipesJob(true))
    ->weekly();
Schedule::job(new SyncRecipesJob())
    ->daily()
    ->days([
        ConsoleSchedule::MONDAY,
        ConsoleSchedule::TUESDAY,
        ConsoleSchedule::WEDNESDAY,
        ConsoleSchedule::THURSDAY,
        ConsoleSchedule::FRIDAY,
        ConsoleSchedule::SATURDAY,
    ]);

Schedule::job(new SyncMenusJob())
    ->twiceDaily();

Schedule::job(new UpdateCountryStatisticsJob())
    ->twiceDaily(4, 16);
