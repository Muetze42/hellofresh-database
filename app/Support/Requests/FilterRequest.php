<?php

namespace App\Support\Requests;

use App\Models\Filter;
use ArgumentCountError;
use BadMethodCallException;
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
    public array $defaults;

    /**
     * The filterable
     */
    public array $filterable;

    /**
     * The request instance.
     */
    protected ?Request $request;

    protected function __construct(Request $request = null)
    {
        $this->request = $request;
        $this->setDefaults();
    }

    /**
     * Set the default filter values.
     */
    public function setDefaults(): void
    {
        $this->filterable = [
            'ingredients',
            'ingredients_except',
            'allergens_except',
            'tags',
            'tags_except',
            'label',
            'label_except',
        ];

        $this->defaults = [
            'allergens_except' => [],
            'difficulties' => [
                'd1' => true,
                'd2' => true,
                'd3' => true,
            ],
            'iMode' => false,
            'ingredients' => [],
            'ingredients_except' => [],
            'label' => [],
            'label_except' => [],
            'pdf' => false,
            'prepTime' => [
                data_get(country()->data, 'prepMin', 0),
                data_get(country()->data, 'prepMax', 0),
            ],
            'tags' => [],
            'tags_except' => [],
        ];
    }

    /**
     * Get the default filter values.
     */
    public static function defaults(): array
    {
        return (new static())->defaults;
    }

    /**
     * Get the filterable.
     */
    public static function filterable(): array
    {
        return (new static())->filterable;
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
        $max = config('application.filter.max_filterable_items', 20);

        $validated = $this->request->validate(
            Arr::mapWithKeys(
                $this->defaults,
                function (mixed $default, string $key) use ($max) {
                    if (is_bool($default)) {
                        return [$key => 'bool'];
                    }
                    if ($key == 'prepTime') {
                        return [$key => 'array|min:2|max:2'];
                    }
                    if ($key == 'difficulties') {
                        return [$key => 'array|min:3|max:3'];
                    }

                    return [$key => 'array', 'max:' . $max];
                }
            )
        );

        $except = [];

        foreach (Arr::only($validated, $this->filterable) as $key => $value) {
            $validated[$key] = Arr::pluck($value, 'id');
        }

        foreach (Arr::only($validated, $this->filterable) as $key => $value) {
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

        if (
            $validated['prepTime'][0] == data_get(country()->data, 'prepMin', 0) &&
            $validated['prepTime'][1] == data_get(country()->data, 'prepMax', 0)
        ) {
            $except[] = 'prepTime';
        }

        $validated = array_filter($validated);
        $validated['difficulties'] = array_filter(
            $validated['difficulties'],
            fn (bool $state) => !$state
        );

        if (count($validated['difficulties']) >= 3 || !count($validated['difficulties'])) {
            $except[] = 'difficulties';
        }

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

        return array_replace_recursive($this->defaults, unserialize($filter->data));
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
            throw new BadMethodCallException(
                sprintf('Call to undefined method %s::%s()', __CLASS__, $method)
            );
        }

        $method = $methods[$method];

        return (new self($args[0]))->$method();
    }
}
