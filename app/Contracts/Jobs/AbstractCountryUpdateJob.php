<?php

namespace App\Contracts\Jobs;

use App\Http\Clients\HelloFreshClient;

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
}
