<?php

use App\Jobs\Country\CountryResourcesOrchestrationJob;
use App\Jobs\Country\UpdateCountryStatisticsJob;
use App\Jobs\GenerateSitemapsJob;
use App\Jobs\Menu\SyncMenusJob;
use App\Jobs\Recipe\SyncRecipesJob;
use Illuminate\Console\Scheduling\Schedule as ConsoleSchedule;
use Illuminate\Support\Facades\Schedule;

Schedule::command('horizon:snapshot')
    ->everyFiveMinutes();

Schedule::command('prune-expired-email-verifications')
    ->daily();

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

Schedule::job(new CountryResourcesOrchestrationJob())
    ->twiceDaily();

Schedule::job(new UpdateCountryStatisticsJob())
    ->twiceDaily(4, 16);

Schedule::job(new GenerateSitemapsJob())
    ->dailyAt(12);
