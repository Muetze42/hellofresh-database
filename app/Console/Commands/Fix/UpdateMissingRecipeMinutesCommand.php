<?php

namespace App\Console\Commands\Fix;

use App\Contracts\Commands\AbstractCountryCommand;
use App\Models\Recipe;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'fix:update-missing-recipe-minutes')]
class UpdateMissingRecipeMinutesCommand extends AbstractCountryCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fix:update-missing-recipe-minutes';

    /**
     * The console command description.
     */
    protected $description = 'Update missing recipe minutes';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Recipe::whereNotNull('prep_time')
            ->whereNull('minutes')
            ->get()
            ->each(function (Recipe $recipe) {
                $prepTime = str_replace('"', '', $recipe->prep_time);
                $minutes = iso8601ToMinutes($prepTime);

                $recipe->update([
                    'minutes' => $minutes > 0 ? $minutes : null,
                    'prep_time' => $prepTime,
                ]);
            });
    }
}
