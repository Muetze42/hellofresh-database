<?php

namespace App\Contracts\Jobs;

use App\Models\Country;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class AbstractCountryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The Country instance.
     */
    public Country $country;

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'hello-fresh-api';

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Set the Country for the job.
     */
    public function onCountry(Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->country->switch();
    }
}
