<?php

namespace App\Console\Commands\HelloFresh;

use App\Console\Contracts\AbstractUpdateCommand;
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
        // Debug
        $this->components->info($this->country->domain);
    }
}
