<?php

namespace App\Jobs\Recipe;

use App\Enums\QueueEnum;
use App\Http\Clients\HelloFresh\HelloFreshClient;
use App\Jobs\Concerns\HandlesApiFailuresTrait;
use App\Models\Country;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class FetchRecipeJob implements ShouldBeUnique, ShouldQueue
{
    use Batchable;
    use HandlesApiFailuresTrait;
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds after which the job's unique lock will be released.
     */
    public int $uniqueFor = 300;

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return $this->country->id . '-' . $this->hellofreshId;
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Country $country,
        public string $locale,
        public string $hellofreshId,
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

        try {
            $recipeData = $client->getRecipe($this->country, $this->locale, $this->hellofreshId)->array();
        } catch (ConnectionException|RequestException $exception) {
            $this->handleApiFailure($exception);

            return;
        }

        ImportRecipeJob::dispatch(
            country: $this->country,
            locale: $this->locale,
            recipe: $recipeData,
            ignoreActive: true,
        );
    }
}
