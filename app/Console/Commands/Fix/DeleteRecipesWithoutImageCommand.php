<?php

namespace App\Console\Commands\Fix;

use App\Contracts\Commands\AbstractCountryCommand;
use App\Models\Recipe;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'fix:delete-recipes-without-image')]
class DeleteRecipesWithoutImageCommand extends AbstractCountryCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fix:delete-recipes-without-image';

    /**
     * The console command description.
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Recipe::whereNull('image_path')->orWhere('image_path', '')->delete();
    }
}
