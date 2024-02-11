<?php

namespace App\Console\Commands\HelloFresh;

use App\Http\Clients\HelloFreshClient;
use App\Models\Country;
use App\Services\HelloFreshService;
use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:hello-fresh:update-recipe')]
class UpdateRecipeCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:hello-fresh:update-recipe
                        {country : The ID of the country}
                        {id : The HelloFresh recipe ID}';

    /**
     * The console command description.
     */
    protected $description = 'Update recipe for selected country';

    /**
     * Execute the console command.
     *
     * @throws \NormanHuth\HellofreshScraper\Exceptions\HellofreshScraperException
     */
    public function handle(): void
    {
        try {
            $country = Country::findOrFail($this->argument('country'));
        } catch (Exception $exception) {
            $this->components->error($exception->getMessage());

            return;
        }

        foreach ($country->locales as $locale) {
            $country->switch($locale);
            $client = new HelloFreshClient(
                isoCountryCode: $country->code,
                isoLocale: $locale
            );

            $item = $client->recipe($this->argument('id'));

            if (!$item) {
                $this->components->error(
                    sprintf('No recipe with the ID `%s` found for this country.', $this->argument('id'))
                );

                return;
            }

            $recipe = HelloFreshService::syncRecipe($item);

            $this->components->info(
                sprintf('Recipe `%s` updated for %s (%s)', $recipe->getKey(), $country->code, $locale)
            );
        }
    }
}
