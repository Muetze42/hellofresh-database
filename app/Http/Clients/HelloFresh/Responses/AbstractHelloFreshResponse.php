<?php

namespace App\Http\Clients\HelloFresh\Responses;

use Illuminate\Http\Client\Response;
use TypeError;

/**
 * @template TData of array
 */
abstract class AbstractHelloFreshResponse extends Response
{
    /**
     * Get the JSON decoded body of the response as an array.
     *
     * @phpstan-return TData
     */
    abstract public function array(): array;

    /**
     * Get the JSON decoded body of the response as an array.
     *
     * @phpstan-return TData
     *
     * @throws TypeError
     */
    protected function toArray(): array
    {
        $value = $this->json();

        if (! is_array($value)) {
            throw new TypeError('The JSON decoded body is not an array.');
        }

        /** @var TData $value */
        return $value;
    }
}
