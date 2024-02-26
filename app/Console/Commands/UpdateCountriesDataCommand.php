<?php

namespace App\Console\Commands;

use App\Contracts\Commands\AbstractCountryCommand;
use App\Models\Recipe;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:update-countries-data')]
class UpdateCountriesDataCommand extends AbstractCountryCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:update-countries-data';

    /**
     * The console command description.
     */
    protected $description = 'Update data for each country';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->components->info(sprintf('Updating data for %s', $this->country->code));

        $min = Recipe::has('ingredients', '>')
            ->whereNotNull('minutes')
            ->where('minutes', '>', 0)
            ->min('minutes');
        $this->info('Min: ' . $min);

        $max = Recipe::has('ingredients', '>')
            ->whereNotNull('minutes')
            ->where('minutes', '>', 0)
            ->max('minutes');
        $this->info('Max: ' . $max);

        $data = array_merge((array) $this->country->data, [
            'prepMin' => $min,
            'prepMax' => $max,
        ]);
        $this->country->update(['data' => $data]);
    }
}
