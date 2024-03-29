<?php

namespace App\Console\Commands\HelloFresh;

use App\Contracts\Commands\AbstractUpdateCommand;
use App\Jobs\HelloFresh\UpdateCuisinesJob;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:hello-fresh:update-cuisines')]
class UpdateCuisinesCommand extends AbstractUpdateCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:hello-fresh:update-cuisines';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        UpdateCuisinesJob::countryDispatch($this->option('limit'));
    }
}
