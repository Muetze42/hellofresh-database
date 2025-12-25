<?php

namespace App\Jobs;

use App\Enums\QueueEnum;
use App\Http\Clients\HelloFresh\HelloFreshClient;
use App\Jobs\Concerns\HandlesApiFailuresTrait;
use App\Models\Country;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Context;

/**
 * @method static void dispatch(Country $country, string $locale, int $skip = 0, bool $paginates = true)
 */
class FetchRecipePageJob implements ShouldQueue
{
    use Batchable;
    use HandlesApiFailuresTrait;
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Country $country,
        public string $locale,
        public int $skip = 0,
        public bool $paginates = true,
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

        Context::add([
            'country' => $this->country->id,
            'locale' => $this->locale,
            'skip' => $this->skip,
        ]);

        try {
            $response = $client->withOutThrow()
                ->getRecipes($this->country, $this->locale, $this->skip);
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

        // Dispatch import jobs directly to their own queue (not batch)
        foreach ($response->items() as $recipe) {
            ImportRecipeJob::dispatch($this->country, $this->locale, $recipe);
        }

        // Only add next page fetch to batch if pagination is enabled
        if ($this->paginates && $response->hasMorePages()) {
            $this->batch()?->add([new self($this->country, $this->locale, $response->nextSkip(), paginates: true)]);
        }
    }
}
