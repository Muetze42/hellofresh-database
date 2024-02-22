<?php

namespace App\Support\Requests;

use App\Models\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * @method static make(\Illuminate\Http\Request $request)
 * @method static parse(\Illuminate\Http\Request $request)
 */
class FilterRequest
{
    /**
     * The default filter values.
     */
    protected array $defaults = [
        'pdf' => false,
        'ingredients' => [],
    ];

    /**
     * Get the record matching the filter request if filter request exists.
     */
    protected function firstOrCreate(Request $request): ?string
    {
        $filter = serialize(Arr::sortRecursive(Arr::whereNotNull($request->only(array_keys($this->defaults)))));

        return !empty($filter) ? Filter::firstOrCreate(['data' => $filter])->getKey() : null;
    }

    /**
     * Get the record matching the filter request if filter request exists.
     */
    public function get(Request $request): array
    {
        if (!$filter = $request->input('filter')) {
            return $this->defaults;
        }

        if (!$filter = Filter::find($filter)) {
            return $this->defaults;
        }

        return array_merge($this->defaults, unserialize($filter->data));
    }

    /**
     * Call a static method on the class.
     */
    public static function __callStatic($method, $args)
    {
        if (!isset($args[0])) {
            throw new \ArgumentCountError(
                'Fatal error: Uncaught ArgumentCountError: ' .
                sprintf('Too few arguments to function test(), 0 passed in %s', __CLASS__)
            );
        }

        if (!$args[0] instanceof Request) {
            throw new \TypeError(__CLASS__ . '::' . $method . '(): Argument 1 ($request) must be of type ' .
                Request::class . ', ' . gettype($args[0]) . ' given');
        }

        $methods = [
            'make' => 'firstOrCreate',
            'parse' => 'get'
        ];

        if (!isset($methods[$method])) {
            throw new \BadMethodCallException(
                sprintf('Call to undefined method %s::%s()', __CLASS__, $method)
            );
        }

        $method = $methods[$method];

        return (new self())->$method(...$args);
    }
}
