<?php

namespace App\Jobs\HelloFresh;

use App\Contracts\Jobs\AbstractCountryJob;
use App\Models\Menu;
use App\Models\Recipe;

/**
 * @method static countryDispatch(int $addWeek)
 */
class UpdateMenuJob extends AbstractCountryJob
{
    public int $addWeek;

    /**
     * Create a new job instance.
     */
    public function __construct(int $addWeek)
    {
        $this->addWeek = $addWeek;
    }

    /**
     * Execute the job.
     *
     * @throws \NormanHuth\HellofreshScraper\Exceptions\HellofreshScraperException
     */
    public function handleCountry(): void
    {
        $response = $this->client->withoutException()->menu($this->addWeek);

        if (empty($response)) {
            return;
        }

        $menu = Menu::updateOrCreate(
            ['year_week' => $response['year'] . $response['weak']],
            ['start' => $response['current']]
        );

        $menu->recipes()->sync(
            Recipe::whereIn('id', $response['ids'])->pluck('id')->toArray()
        );
    }
}
