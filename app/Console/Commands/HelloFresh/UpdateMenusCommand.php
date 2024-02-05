<?php

namespace App\Console\Commands\HelloFresh;

use App\Contracts\Commands\AbstractUpdateCommand;
use App\Jobs\HelloFresh\UpdateMenuJob;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:hello-fresh:update-menus')]
class UpdateMenusCommand extends AbstractUpdateCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:hello-fresh:update-menus';

    protected bool $considerLanguages = false;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            UpdateMenuJob::countryDispatch($i);
        }
    }
}
