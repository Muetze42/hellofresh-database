<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class LengthAwarePaginator extends Paginator
{
    /**
     * The number of links to display on each side of current page link.
     */
    public $onEachSide = 1;

    /**
     * Get the paginator links as a collection (for JSON responses).
     *
     * @noinspection DuplicatedCode
     */
    public function linkCollection(): Collection
    {
        return collect($this->elements())->flatMap(function ($item) {
            if (!is_array($item)) {
                return [['url' => null, 'label' => '...', 'active' => false]];
            }

            return collect($item)->map(function ($url, $page) {
                return [
                    'url' => $url,
                    'label' => (string) $page,
                    'active' => $this->currentPage() === $page,
                ];
            });
        })->prepend([
            'url' => $this->previousPageUrl(),
            'label' => function_exists('__') ? __('Previous') : 'Previous',
            'active' => false,
        ])->push([
            'url' => $this->nextPageUrl(),
            'label' => function_exists('__') ? __('Next') : 'Next',
            'active' => false,
        ]);
    }

    /**
     * Get the URL for a given page number.
     */
    public function url($page): string
    {
        if ($page <= 0) {
            $page = 1;
        }

        // If we have any extra query string key / value pairs that need to be added
        // onto the URL, we will put them in query string form and then attach it
        // to the URL. This allows for extra information like sorting storage.
        $parameters = $page == 1 ? [] : [$this->pageName => $page];

        if (count($this->query) > 0) {
            $parameters = array_merge($this->query, $parameters);
        }

        return $this->path()
            . (str_contains($this->path(), '?') ? '&' : '?')
            . Arr::query($parameters)
            . $this->buildFragment();
    }
}
