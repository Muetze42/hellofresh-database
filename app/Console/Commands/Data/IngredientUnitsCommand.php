<?php

namespace App\Console\Commands\Data;

use App\Models\Country;
use App\Models\Recipe;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'data:ingredient-units')]
class IngredientUnitsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'data:ingredient-units';

    /**
     * The console command description.
     */
    protected $description = 'Store all ingredient units grouped by country and language';

    /**
     * The data array.
     */
    protected array $data = [];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Country::each(function (Country $country) {
            foreach ($country->locales as $locale) {
                $country->switch($locale);
                $this->data[$country->code][$locale] = Recipe::pluck('yields')
                    ->flatten(1)
                    ->pluck('ingredients')
                    ->flatten(1)
                    ->pluck('unit')
                    ->unique()
                    ->sort()
                    ->values()
                    ->toArray();
            }
        });

        file_put_contents(
            base_path('data/units.json'),
            json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }
}
