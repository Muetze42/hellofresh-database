<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Schedule::command('app:hello-fresh:update-recipes', [
    '--limit' => 100,
])->days([1, 2, 3, 4, 5, 6])->at('3:00');
Schedule::command('app:hello-fresh:update-recipes')
    ->weeklyOn(0, '3:00');

Schedule::command('app:assets:generate-social-preview')
    ->twiceDailyAt(6, 18);

Schedule::command('app:hello-fresh:update-menus')
    ->dailyAt('6:00');

Schedule::command('app:update-disposable-email-domains')
    ->weekly();

Schedule::command('auth:clear-resets')
    ->everyFifteenMinutes();

Schedule::command('queue:work', [
    '--queue' => 'hellofresh',
    '--timeout' => 0,
    '--sleep' => 1,
    '--stop-when-empty',
])->everyMinute()->withoutOverlapping()
    ->when(function () {
        return $this->app['config']->get('queue.default') == 'database';
    });
