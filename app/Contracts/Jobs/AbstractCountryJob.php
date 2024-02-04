<?php

namespace App\Contracts\Jobs;

use App\Models\Country;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
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
     * Dispatch the job with the given arguments.
     */
    public static function countryDispatch(...$arguments): PendingDispatch
    {
        $instance = new static(...$arguments);
        $instance->withCountry(country());
        $instance->onQueue('hellofresh');

        return new PendingDispatch($instance);
    }

    /**
     * Set the Country the job.
     */
    public function withCountry(Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Execute the job.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function handle(): void
    {
        $this->country->switch();
        $this->handleCountry();
    }
}
