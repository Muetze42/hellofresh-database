<?php

namespace App\Contracts\Jobs;

use App\Http\Clients\HelloFreshClient;
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
     * The country locale.
     */
    public string $locale;

    /**
     * The HelloFreshClient instance.
     */
    public HelloFreshClient $client;

    /**
     * Dispatch the job with the given arguments.
     */
    public static function countryDispatch(...$arguments): PendingDispatch
    {
        $instance = new static(...$arguments);
        $instance->withCountry(country());
        $instance->withLocale(app()->getLocale());
        $instance->onQueue('hellofresh');

        return new PendingDispatch($instance);
    }

    /**
     * Set the Country for the job.
     */
    public function withCountry(Country $country): static
    {
        $this->code = $country;

        return $this;
    }

    /**
     * Set the country locale the job.
     */
    public function withLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->code->switch($this->locale);
        $this->client = new HelloFreshClient(
            isoCountryCode: $this->code->code,
            isoLocale: $this->locale,
            take: $this->code->take,
            baseUrl:$this->code->domain
        );
        $this->handleCountry();
    }

    abstract public function handleCountry(): void;
}
