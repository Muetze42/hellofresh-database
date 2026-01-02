<?php

namespace App\Jobs\Menu;

use App\Enums\QueueEnum;
use App\Http\Clients\HelloFresh\HelloFreshClient;
use App\Jobs\Concerns\HandlesApiFailuresTrait;
use App\Jobs\Recipe\FetchRecipeJob;
use App\Models\Country;
use App\Models\Menu;
use App\Models\Recipe;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Context;
use Throwable;

class FetchMenusJob implements ShouldQueue
{
    use Batchable;
    use HandlesApiFailuresTrait;
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

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
     *
     * @throws Throwable
     */
    protected function importMenu(array $menuData): void
    {
        // Find existing recipes in the database
        $existingHellofreshIds = Recipe::where('country_id', $this->country->id)
            ->whereIn('hellofresh_id', $menuData['recipe_ids'])
            ->pluck('hellofresh_id')
            ->toArray();

        // Find missing recipe IDs
        $missingHellofreshIds = array_values(array_diff($menuData['recipe_ids'], $existingHellofreshIds));

        /** @var Menu $menu */
        $menu = $this->country->menus()->updateOrCreate(
            ['year_week' => $menuData['year_week']],
            ['start' => $menuData['start']]
        );

        // No missing recipes - sync directly
        if ($missingHellofreshIds === []) {
            $recipeIds = Recipe::where('country_id', $this->country->id)
                ->whereIn('hellofresh_id', $menuData['recipe_ids'])
                ->pluck('id')
                ->toArray();

            if ($recipeIds !== []) {
                $menu->recipes()->sync($recipeIds);
            }

            return;
        }

        // Dispatch batch to fetch missing recipes, then sync menu
        $jobs = [];
        foreach ($missingHellofreshIds as $missingHellofreshId) {
            $jobs[] = new FetchRecipeJob(
                country: $this->country,
                locale: $this->locale,
                hellofreshId: $missingHellofreshId,
            );
        }

        Bus::batch($jobs)
            ->name(sprintf('Fetch missing recipes for menu %d', $menu->year_week))
            ->onQueue(QueueEnum::HelloFresh->value)
            ->then(fn () => SyncMenuRecipesJob::dispatch($menu, $this->country->id, $menuData['recipe_ids']))
            ->dispatch();
    }
}
