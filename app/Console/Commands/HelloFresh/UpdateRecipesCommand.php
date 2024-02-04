<?php

namespace App\Console\Commands\HelloFresh;

use App\Contracts\Commands\AbstractUpdateCommand;
use App\Jobs\HelloFresh\UpdateRecipesJob;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:hello-fresh:update-recipes')]
class UpdateRecipesCommand extends AbstractUpdateCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:hello-fresh:update-recipes';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        UpdateRecipesJob::countryDispatch($this->option('limit'));
    }
}
