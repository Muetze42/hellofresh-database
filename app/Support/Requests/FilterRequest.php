<?php

namespace App\Support\Requests;

use App\Models\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * @method static make(\Illuminate\Http\Request $request): ?string
 * phpcs:disable
 * @method static parse(\Illuminate\Http\Request $request): array{pdf: bool, iMode: bool, ingredients: array, ingredients_not: array}
 * phpcs:enable
 */
class FilterRequest
{
    /**
     * The default filter values.
     */
    protected array $defaults = [
        'pdf' => false,
        'iMode' => false,
        'ingredients' => [],
        'ingredients_not' => [],
        'allergens' => [],
        'tags' => [],
    ];

    /**
     * The request instance.
     */
    protected Request $request;

    protected function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the record matching the filter request if filter request exists.
     */
    protected function firstOrCreate(): ?string
    {
        $filtered = $this->filtered();

        if (empty($filtered)) {
            return null;
        }

        $filter = serialize($filtered);

        return Filter::firstOrCreate(['data' => $filter])->getKey();
    }

    /**
     * Get validated and filtered request data.
     */
    protected function filtered(): array
    {
        $filterableItemsRules = ['array', 'max:' . config('application.max_filterable_items')];

        $validated = $this->request->validate([
            'pdf' => 'bool',
            'iMode' => 'bool',
            'ingredients' => $filterableItemsRules,
            'ingredients_not' => $filterableItemsRules,
            'allergens' => $filterableItemsRules,
        ]);

        $except = [];

        foreach ($validated as $key => $value) {
            if (is_array($value)) {
                $validated[$key] = Arr::pluck($value, 'id');
            }
        }

        if (empty($validated['ingredients']) && empty($validated['ingredients_not'])) {
            $except[] = 'iMode';
        }

        $validated = array_filter($validated);

        return Arr::except(Arr::sortRecursive(Arr::whereNotNull($validated)), $except);
    }

    /**
     * Get the record matching the filter request if filter request exists.
     *
     * @return array{
     *     pdf: bool,
     *     iMode: bool,
     *     ingredients: array,
     *     ingredients_not: array,
     *     allergens: array
     * }
     */
    public function get(): array
    {
        if (!$filter = $this->request->input('filter')) {
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
            'parse' => 'get',
        ];

        if (!isset($methods[$method])) {
            throw new \BadMethodCallException(
                sprintf('Call to undefined method %s::%s()', __CLASS__, $method)
            );
        }

        $method = $methods[$method];

        return (new self($args[0]))->$method();
    }
}
