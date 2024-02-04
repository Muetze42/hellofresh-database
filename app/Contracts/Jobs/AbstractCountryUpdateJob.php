<?php

namespace App\Contracts\Jobs;

use NormanHuth\HellofreshScraper\Http\Responses\AbstractIndexResponse;

/**
 * @method static countryDispatch(?int $limit = null, int $skip = 0)
 */
abstract class AbstractCountryUpdateJob extends AbstractCountryJob
{
    public ?int $limit;

    public int $skip;

    /**
     * Create a new job instance.
     */
    protected function __construct(?int $limit = null, int $skip = 0)
    {
        $this->limit = $limit;
        $this->skip = $skip;
    }

    /**
     * Dispatch optional a new job.
     */
    protected function afterCountryHandle(AbstractIndexResponse $response): void
    {
        if ($this->limit && ($response->skip() + $response->take()) >= $this->limit) {
            return;
        }

        if ($next = $response->getNextPaginate()) {
            static::countryDispatch($this->limit, $next);
        }
    }
}
