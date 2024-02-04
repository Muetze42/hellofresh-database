<?php

namespace App\Contracts\Jobs;

use App\Http\Clients\HelloFreshClient;

abstract class AbstractCountryUpdateJob extends AbstractCountryJob
{
    public ?int $limit;

    public int $skip;

    public HelloFreshClient $client;

    /**
     * Create a new job instance.
     */
    protected function __construct(?int $limit = null, int $skip = 0)
    {
        $this->limit = $limit;
        $this->skip = $skip;
        $this->client = new HelloFreshClient();
    }
}
