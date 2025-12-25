<?php

namespace App\Jobs\Concerns;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

trait HandlesApiFailuresTrait
{
    /**
     * Handle a failed API request with logging and retry logic.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    protected function handleApiFailure(ConnectionException|RequestException $exception): void
    {
        $isLastAttempt = $this->attempts() >= $this->tries;

        if ($isLastAttempt) {
            throw $exception;
        }

        Log::warning(static::class . ' failed, retrying', [
            'attempt' => $this->attempts(),
            'exception' => $exception->getMessage(),
        ]);

        $this->release(30);
    }
}
