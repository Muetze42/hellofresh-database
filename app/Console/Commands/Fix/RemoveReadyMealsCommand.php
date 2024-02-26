<?php

namespace App\Console\Commands\Fix;

use App\Contracts\Commands\AbstractCountryCommand;
use App\Models\Recipe;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'fix:remove-ready-meals')]
class RemoveReadyMealsCommand extends AbstractCountryCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fix:remove-ready-meals';

    /**
     * The console command description.
     */
    protected $description = 'Remove ready meals.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Recipe::has('ingredients', '<', 4)->delete();
    }
}
