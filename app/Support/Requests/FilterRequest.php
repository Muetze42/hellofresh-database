<?php

namespace App\Support\Requests;

use App\Models\Filter;
use ArgumentCountError;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * @method static make(\Illuminate\Http\Request $request): ?string
 * phpcs:disable
 * @method static parse(\Illuminate\Http\Request $request): array{pdf: bool, iMode: bool, ingredients: array, ingredients_except: array, tags: array, allergens: array}
 * phpcs:enable
 */
class FilterRequest
{
    /**
     * The default filter values.
     */
    public array $defaults = [
        'pdf' => false,
        'iMode' => false,
        'ingredients' => [],
        'ingredients_except' => [],
        'allergens_except' => [],
        'tags' => [],
        'tags_except' => [],
        'labels' => [],
        'labels_except' => [],
    ];

    /**
     * The request instance.
     */
    protected ?Request $request;

    protected function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * Get the default filter values.
     */
    public static function defaults(): array
    {
        return (new static())->defaults;
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
        $validated = $this->request->validate(
            Arr::mapWithKeys(
                $this->defaults,
                fn (mixed $default, string $key) => !is_array($default) ? [$key => 'bool'] :
                    [$key => ['array', 'max:' . config('application.filter.max_filterable_items', 20)]]
            )
        );

        $except = [];

        foreach ($validated as $key => $value) {
            if (is_array($value)) {
                $validated[$key] = Arr::pluck($value, 'id');
            }
        }

        foreach ($validated as $key => $value) {
            if (str_ends_with($key, '_except')) {
                $nonExceptKey = substr($key, 0, -7);
                if (empty($validated[$nonExceptKey])) {
                    continue;
                }
                $validated[$key] = array_diff($value, $validated[$nonExceptKey]);
                $validated[$nonExceptKey] = array_diff($validated[$nonExceptKey], $value);
            }
        }

        if (empty($validated['ingredients'])) {
            $except[] = 'iMode';
        }

        $validated = array_filter($validated);

        return Arr::except(Arr::sortRecursive($validated), $except);
    }

    /**
     * Get the record matching the filter request if filter request exists.
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
            throw new ArgumentCountError(
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
