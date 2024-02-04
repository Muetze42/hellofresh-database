<?php

namespace App\Console\Commands\HelloFresh;

use App\Contracts\Commands\AbstractUpdateCommand;
use App\Jobs\HelloFresh\UpdateAllergensJob;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:hello-fresh:update-allergens')]
class UpdateAllergensCommand extends AbstractUpdateCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:hello-fresh:update-allergens';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        UpdateAllergensJob::countryDispatch($this->option('limit'));
    }
}
