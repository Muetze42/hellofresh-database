<?php

namespace App\Jobs;

use App\Enums\QueueEnum;
use App\Http\Clients\HelloFresh\HelloFreshClient;
use App\Models\Country;
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
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

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

        $locale = $this->country->locales[0] ?? 'en';

        Context::add([
            'country' => $this->country->id,
            'locale' => $locale,
        ]);

        // Fetch 1 week back and up to 6 weeks ahead
        for ($addWeeks = -1; $addWeeks <= 6; $addWeeks++) {
            if ($this->batch()?->cancelled()) {
                return;
            }

            $week = now()->startOfWeek()->addWeeks($addWeeks);
            $weekString = sprintf('%d-W%02d', $week->format('o'), $week->format('W'));

            $response = $client->getMenus($this->country, $locale, $weekString);

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
        $recipeIds = Recipe::where('country_id', $this->country->id)
            ->whereIn('hellofresh_id', $menuData['recipe_ids'])
            ->pluck('id')
            ->toArray();

        if ($recipeIds === []) {
            return;
        }

        /* @var \App\Models\Menu $menu */
        $menu = $this->country->menus()->updateOrCreate(
            ['year_week' => $menuData['year_week']],
            ['start' => $menuData['start']]
        );

        $menu->recipes()->sync($recipeIds);
    }
}
