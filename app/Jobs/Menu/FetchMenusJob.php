<?php

namespace App\Jobs\Menu;

use App\Enums\QueueEnum;
use App\Http\Clients\HelloFresh\HelloFreshClient;
use App\Jobs\Concerns\HandlesApiFailuresTrait;
use App\Jobs\Recipe\ImportRecipeJob;
use App\Models\Country;
use App\Models\Menu;
use App\Models\Recipe;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Context;

class FetchMenusJob implements ShouldQueue
{
    use Batchable;
    use HandlesApiFailuresTrait;
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    protected HelloFreshClient $client;

    protected string $locale;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Country $country,
    ) {
        $this->onQueue(QueueEnum::HelloFresh->value);
    }

    /**
     * Execute the job.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function handle(HelloFreshClient $client): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $this->client = $client;
        $this->locale = $this->country->locales[0] ?? 'en';

        Context::add([
            'country' => $this->country->id,
            'locale' => $this->locale,
        ]);

        // Fetch 1 week back and up to 6 weeks ahead
        for ($addWeeks = -1; $addWeeks <= 6; $addWeeks++) {
            if ($this->batch()?->cancelled()) {
                return;
            }

            $week = now()->startOfWeek()->addWeeks($addWeeks);
            $weekString = sprintf('%d-W%02d', $week->format('o'), $week->format('W'));

            try {
                $response = $client->withOutThrow()
                    ->getMenus($this->country, $this->locale, $weekString);
            } catch (ConnectionException $connectionException) {
                $this->handleApiFailure($connectionException);

                return;
            }

            if ($response->failed()) {
                $exception = $response->toException();
                assert($exception !== null);

                $this->handleApiFailure($exception);

                return;
            }

            foreach ($response->menus() as $menuData) {
                $this->importMenu($menuData);
            }
        }
    }

    /**
     * Import a single menu with its recipes.
     *
     * @param  array{week: string, year_week: int, start: string, recipe_ids: list<string>}  $menuData
     */
    protected function importMenu(array $menuData): void
    {
        // Find existing recipes in the database
        $existingRecipes = Recipe::where('country_id', $this->country->id)
            ->whereIn('hellofresh_id', $menuData['recipe_ids'])
            ->pluck('id', 'hellofresh_id')
            ->toArray();

        // Find missing recipe IDs
        $missingHellofreshIds = array_values(array_diff($menuData['recipe_ids'], array_keys($existingRecipes)));

        // Fetch and import missing recipes
        $this->importMissingRecipes($missingHellofreshIds);

        // Re-fetch recipe IDs after importing missing ones
        $recipeIds = Recipe::where('country_id', $this->country->id)
            ->whereIn('hellofresh_id', $menuData['recipe_ids'])
            ->pluck('id')
            ->toArray();

        if ($recipeIds === []) {
            return;
        }

        /** @var Menu $menu */
        $menu = $this->country->menus()->updateOrCreate(
            ['year_week' => $menuData['year_week']],
            ['start' => $menuData['start']]
        );

        $menu->recipes()->sync($recipeIds);
    }

    /**
     * Fetch and import missing recipes from the API.
     *
     * @param  list<string>  $hellofreshIds
     */
    protected function importMissingRecipes(array $hellofreshIds): void
    {
        foreach ($hellofreshIds as $hellofreshId) {
            try {
                $recipeData = $this->client->getRecipe($this->country, $this->locale, $hellofreshId)->array();

                ImportRecipeJob::dispatchSync(
                    country: $this->country,
                    locale: $this->locale,
                    recipe: $recipeData,
                    ignoreActive: true,
                );
            } catch (ConnectionException|RequestException) {
                // Skip recipes that can't be fetched
                continue;
            }
        }
    }
}
